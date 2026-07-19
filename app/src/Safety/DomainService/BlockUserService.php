<?php

declare(strict_types=1);

namespace App\Safety\DomainService;

use App\Models\Block;
use App\Models\Connection;
use App\Models\User;
use App\Safety\RequestHandler\BlockUser\BlockUserCommand;
use Illuminate\Support\Carbon;

final class BlockUserService
{
    public function handle(BlockUserCommand $command): Block
    {
        if ($command->blockerUserId === $command->blockedUserId) {
            throw new \InvalidArgumentException('Cannot block own account.');
        }

        $blockedUser = User::query()->findOrFail($command->blockedUserId);
        $blockerProfile = User::query()->findOrFail($command->blockerUserId)->datingProfile()->first();
        $blockedProfile = $blockedUser->datingProfile()->first();

        $block = Block::query()->updateOrCreate([
            'blocker_user_id' => $command->blockerUserId,
            'blocked_user_id' => $command->blockedUserId,
        ], [
            'reason_category' => $command->reasonCategory,
        ]);

        if (null !== $blockerProfile && null !== $blockedProfile) {
            $this->blockConnection((int) $blockerProfile->getKey(), (int) $blockedProfile->getKey());
        }

        return $block;
    }

    private function blockConnection(int $firstProfileId, int $secondProfileId): void
    {
        $profileIds = [$firstProfileId, $secondProfileId];
        sort($profileIds);

        Connection::query()
            ->where('profile_one_id', $profileIds[0])
            ->where('profile_two_id', $profileIds[1])
            ->update([
                'status' => 'blocked',
                'blocked_at' => Carbon::now(),
            ]);
    }
}
