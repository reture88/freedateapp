<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminUserUpdateController
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => 'required|string|in:admin,customer',
            'is_active' => 'nullable',
        ]);

        $role = (string) $data['role'];
        $isActive = isset($data['is_active']);

        $user->forceFill([
            'role' => $role,
            'is_admin' => 'admin' === $role,
            'is_active' => $isActive,
        ])->save();

        return back()->with('success', 'User updated.');
    }
}
