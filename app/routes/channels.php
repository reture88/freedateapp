<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});


Broadcast::channel('global-channel', static function () {
    return true;
});

Broadcast::channel('conversations.{conversation}', static function (User $user, int $conversation): bool {
    return Conversation::query()
        ->whereKey($conversation)
        ->whereHas('connection', static function ($query) use ($user): void {
            $query->whereHas('profileOne', static fn ($profileQuery) => $profileQuery->where('user_id', $user->getKey()))
                ->orWhereHas('profileTwo', static fn ($profileQuery) => $profileQuery->where('user_id', $user->getKey()));
        })
        ->exists();
});

Broadcast::channel('users.{user}', static fn (User $authenticatedUser, int $user): bool => (int) $authenticatedUser->getKey() === $user);

Broadcast::channel('admin.reports', static fn (User $user): bool => $user->canAccessAdminPanel());
