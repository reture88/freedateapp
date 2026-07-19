<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\User;
use App\User\DomainService\Exception\EmailAlreadyTakenException;
use App\User\Dto\ValueObject\EmailAddress;
use App\User\Dto\ValueObject\Name;

final readonly class UpdateProfileService
{
    public function handle(User $user, Name $name, EmailAddress $email): User
    {
        if (User::query()
            ->where('email', $email->value())
            ->whereKeyNot($user->getKey())
            ->exists()
        ) {
            throw EmailAlreadyTakenException::create($email);
        }

        $user->forceFill([
            'name' => $name->value(),
            'email' => $email->value(),
        ])->save();

        return $user;
    }
}
