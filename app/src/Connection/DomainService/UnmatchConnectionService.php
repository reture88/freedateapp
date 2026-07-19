<?php

declare(strict_types=1);

namespace App\Connection\DomainService;

use App\Connection\RequestHandler\UnmatchConnection\UnmatchConnectionCommand;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Support\Carbon;

final class UnmatchConnectionService
{
    public function handle(UnmatchConnectionCommand $command): Connection
    {
        $viewerProfile = User::query()->findOrFail($command->userId)->datingProfile()->firstOrFail();
        $connection = Connection::query()->findOrFail($command->connectionId);

        if ((int) $connection->profile_one_id !== (int) $viewerProfile->getKey() && (int) $connection->profile_two_id !== (int) $viewerProfile->getKey()) {
            throw new \InvalidArgumentException('Connection is not available.');
        }

        if ('matched' !== $connection->status) {
            return $connection;
        }

        $connection->forceFill([
            'status' => 'unmatched',
            'unmatched_at' => Carbon::now(),
        ])->save();

        return $connection;
    }
}
