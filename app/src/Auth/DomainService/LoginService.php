<?php

declare(strict_types=1);

namespace App\Auth\DomainService;

use App\Auth\DomainService\Exception\InvalidCredentialsException;
use App\Models\User;
use App\User\Dto\ValueObject\EmailAddress;
use App\User\Dto\ValueObject\PlainPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final readonly class LoginService
{
    public function handle(EmailAddress $email, PlainPassword $password, bool $remember = false): User
    {
        if (!Auth::attempt(['email' => $email->value(), 'password' => $password->value(), 'is_active' => true], $remember)) {
            throw InvalidCredentialsException::create();
        }

        Session::regenerate();

        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
