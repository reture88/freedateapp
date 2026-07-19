<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\User;
use App\User\DomainService\Exception\CurrentPasswordMismatchException;
use App\User\DomainService\Exception\PasswordConfirmationMismatchException;
use App\User\Dto\ValueObject\PlainPassword;
use Illuminate\Support\Facades\Hash;

final readonly class ChangePasswordService
{
    public function handle(User $user, PlainPassword $currentPassword, PlainPassword $password, PlainPassword $passwordConfirmation): void
    {
        if (!Hash::check($currentPassword->value(), (string) $user->password)) {
            throw CurrentPasswordMismatchException::create();
        }

        if ($password->value() !== $passwordConfirmation->value()) {
            throw PasswordConfirmationMismatchException::create();
        }

        $user->forceFill([
            'password' => Hash::make($password->value()),
        ])->save();
    }
}
