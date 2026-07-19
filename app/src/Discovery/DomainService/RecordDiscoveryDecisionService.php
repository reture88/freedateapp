<?php

declare(strict_types=1);

namespace App\Discovery\DomainService;

use App\Models\Connection;
use App\Models\DatingProfile;
use App\Models\DiscoveryDecision;
use App\Models\User;
use App\Discovery\RequestHandler\RecordDiscoveryDecision\RecordDiscoveryDecisionCommand;
use Illuminate\Support\Carbon;

final class RecordDiscoveryDecisionService
{
    public function handle(RecordDiscoveryDecisionCommand $command): DiscoveryDecision
    {
        $viewer = User::query()->findOrFail($command->userId)->datingProfile()->firstOrFail();
        $target = DatingProfile::query()->findOrFail($command->targetProfileId);

        if ((int) $viewer->getKey() === (int) $target->getKey()) {
            throw new \InvalidArgumentException('Cannot decide on own profile.');
        }

        $decision = DiscoveryDecision::query()->updateOrCreate([
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $target->getKey(),
        ], [
            'decision' => $command->decision,
            'decided_at' => Carbon::now(),
        ]);

        if ('accepted' === $command->decision) {
            $this->recordAcceptedConnection($viewer, $target);
        }

        return $decision;
    }

    private function recordAcceptedConnection(DatingProfile $viewer, DatingProfile $target): void
    {
        $profileIds = [(int) $viewer->getKey(), (int) $target->getKey()];
        sort($profileIds);

        $connection = Connection::query()->firstOrCreate([
            'profile_one_id' => $profileIds[0],
            'profile_two_id' => $profileIds[1],
        ]);

        $decisionColumn = (int) $viewer->getKey() === (int) $connection->profile_one_id
            ? 'profile_one_decision'
            : 'profile_two_decision';

        $connection->forceFill([$decisionColumn => 'accepted']);

        if ('accepted' === $connection->profile_one_decision && 'accepted' === $connection->profile_two_decision) {
            $connection->forceFill([
                'status' => 'matched',
                'matched_at' => $connection->matched_at ?? Carbon::now(),
            ]);
        }

        $connection->save();

        if ('matched' === $connection->status) {
            $connection->conversation()->firstOrCreate([]);
        }
    }
}
