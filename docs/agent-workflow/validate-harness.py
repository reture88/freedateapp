#!/usr/bin/env python3
"""Static validator for the Freedateapp Qwen agent harness.

Run from the repository root:
    python3 docs/agent-workflow/validate-harness.py

Checks agent/skill/rule frontmatter, result-contract fields, JSON, and
referenced-path existence. It does not call Qwen Code and does not modify files.
"""

from __future__ import annotations

import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
AGENTS = sorted((ROOT / ".qwen" / "agents").glob("*.md"))
SKILLS = sorted((ROOT / ".qwen" / "skills").glob("*/SKILL.md"))
RULES = sorted((ROOT / ".qwen" / "rules").glob("*.md"))

KNOWN_TOOLS = {
    "read_file", "grep_search", "glob", "read_many_files", "write_file",
    "edit", "notebook_edit", "run_shell_command", "agent", "list_agents",
    "send_message", "tool_search", "tool_call", "skill", "mcp__figma",
    "mcp__playwright", "web_fetch", "read_mcp_resource", "zoom_image",
    "report_findings", "loop_wakeup", "cron_create", "cron_delete",
    "task_stop", "monitor", "propose_goal", "update_goal",
    "enter_worktree", "exit_worktree",
}
STATUSES = {
    "pending", "in_progress", "blocked", "awaiting_approval", "completed",
    "failed", "rejected",
}
RESULT_FIELDS = [
    "Task ID", "Agent role", "Status", "Summary", "Required context",
    "Files inspected", "Missing context", "Context status", "Files changed",
    "Decisions made", "Outstanding questions", "Risks",
    "Verification evidence", "Next recommended action",
]
BUILTIN_AGENTS = {"general-purpose", "Explore", "statusline-setup",
                  "review-agent", "claude-code", "codex"}

errors: list[str] = []
warnings: list[str] = []


def parse_frontmatter(path: Path) -> tuple[dict, str]:
    text = path.read_text(encoding="utf-8")
    if not text.startswith("---\n"):
        errors.append(f"{path}: missing frontmatter")
        return {}, text
    end = text.find("\n---\n", 4)
    if end == -1:
        errors.append(f"{path}: unterminated frontmatter")
        return {}, text
    data: dict = {}
    current_list = None
    for line in text[4:end].splitlines():
        if not line.strip() or line.lstrip().startswith("#"):
            continue
        if line.startswith("  - ") and current_list:
            data[current_list].append(line[4:].strip().strip("'\""))
            continue
        match = re.match(r"^([A-Za-z0-9_-]+):\s*(.*)$", line)
        if not match:
            errors.append(f"{path}: unparsed frontmatter line {line!r}")
            continue
        key, value = match.group(1), match.group(2).strip()
        if value == "":
            current_list = key
            data[key] = []
        else:
            current_list = None
            data[key] = value.strip("'\"")
    return data, text[end + 5:]


def check_agents() -> None:
    names = set()
    for path in AGENTS:
        data, text = parse_frontmatter(path)
        for key in ("name", "description", "model", "approvalMode"):
            if not data.get(key):
                errors.append(f"{path}: missing {key}")
        name = data.get("name", "")
        if not re.fullmatch(r"[a-z0-9][a-z0-9-]*", name):
            errors.append(f"{path}: invalid agent name {name!r}")
        if name in names:
            errors.append(f"{path}: duplicate agent name {name}")
        if name in BUILTIN_AGENTS:
            errors.append(f"{path}: collides with built-in agent {name}")
        names.add(name)
        if len(data.get("description", "")) > 1000:
            warnings.append(f"{path}: description over 1000 chars")
        if data.get("approvalMode") not in {"default", "plan", "auto-edit",
                                            "auto", "yolo", "bubble"}:
            errors.append(f"{path}: invalid approvalMode {data.get('approvalMode')!r}")
        if "tools" not in data and "disallowedTools" not in data:
            errors.append(f"{path}: needs tools or disallowedTools")
        for key in ("tools", "disallowedTools"):
            for tool in data.get(key, []) or []:
                if tool not in KNOWN_TOOLS and not tool.startswith("mcp__"):
                    errors.append(f"{path}: unknown {key} entry {tool!r}")
        start = text.find("## Result contract")
        match = re.search(r"```text\n(.*?)```", text[start:], re.S) if start >= 0 else None
        if not match:
            errors.append(f"{path}: missing result contract block")
            continue
        block = match.group(1)
        for field in RESULT_FIELDS:
            if not re.search(rf"^{re.escape(field)}:", block, re.M):
                errors.append(f"{path}: result contract missing {field}")
        status_line = re.search(r"^Status: (.+)$", block, re.M)
        if not status_line:
            errors.append(f"{path}: result contract missing Status values")
        else:
            values = {part.strip() for part in status_line.group(1).split("|")}
            unknown = values - STATUSES
            if unknown:
                errors.append(f"{path}: unknown status values {sorted(unknown)}")


def check_skills() -> None:
    for path in SKILLS:
        data, _ = parse_frontmatter(path)
        if not data.get("name"):
            errors.append(f"{path}: missing name")
        if not data.get("description"):
            errors.append(f"{path}: missing description")
        if data.get("name") and not re.fullmatch(r"[A-Za-z0-9_.:-]+", data["name"]):
            errors.append(f"{path}: invalid skill name {data['name']!r}")


def check_rules() -> None:
    for path in RULES:
        data, _ = parse_frontmatter(path)
        if not data.get("description"):
            errors.append(f"{path}: missing description")
        if not data.get("paths"):
            errors.append(f"{path}: missing paths")


def check_settings() -> None:
    path = ROOT / ".qwen" / "settings.json"
    try:
        json.loads(path.read_text(encoding="utf-8"))
    except json.JSONDecodeError as exc:
        errors.append(f"{path}: invalid JSON: {exc}")


def check_references() -> None:
    files = AGENTS + SKILLS + RULES + [ROOT / "AGENTS.md",
                                       ROOT / "docs" / "harness.md"]
    files += sorted((ROOT / "docs" / "agent-workflow").glob("*.md"))
    pattern = re.compile(r"`([A-Za-z0-9_.<>-]+(?:/[A-Za-z0-9_.<>*-]+)+)`")
    for path in files:
        for ref in set(pattern.findall(path.read_text(encoding="utf-8"))):
            if any(token in ref for token in ("<", "*")):
                continue
            if ref.startswith(("docs/work/", "docs/superpowers/", "http")):
                continue
            if ref.startswith((".windsurf/", "Presentation/", "tests/",
                               ".claude/")):
                continue
            if ref.endswith("docs/context.md"):
                continue
            if not (ROOT / ref).exists():
                warnings.append(f"{path.relative_to(ROOT)}: missing reference {ref}")


def main() -> int:
    check_agents()
    check_skills()
    check_rules()
    check_settings()
    check_references()
    print(f"agents: {len(AGENTS)}, skills: {len(SKILLS)}, rules: {len(RULES)}")
    print(f"errors: {len(errors)}, warnings: {len(warnings)}")
    for item in errors:
        print(f"  ERROR {item}")
    for item in warnings:
        print(f"  WARN  {item}")
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
