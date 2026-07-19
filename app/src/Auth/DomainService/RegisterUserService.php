<?php

declare(strict_types=1);

namespace App\Auth\DomainService;

use App\Auth\DomainService\Exception\EmailAlreadyRegisteredException;
use App\Auth\DomainService\Exception\PasswordConfirmationException;
use App\Models\User;
use App\User\Dto\ValueObject\EmailAddress;
use App\User\Dto\ValueObject\Name;
use App\User\Dto\ValueObject\PlainPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

final readonly class RegisterUserService
{
    public function handle(Name $name, EmailAddress $email, PlainPassword $password, PlainPassword $passwordConfirmation): User
    {
        if ($password->value() !== $passwordConfirmation->value()) {
            throw PasswordConfirmationException::mismatch();
        }

        if (User::query()->where('email', $email->value())->exists()) {
            throw EmailAlreadyRegisteredException::forEmail($email);
        }

        $user = User::query()->create([
            'name' => $name->value(),
            'email' => $email->value(),
            'password' => Hash::make($password->value()),
        ]);

        Auth::login($user);
        Session::regenerate();

        return $user;
    }
}
