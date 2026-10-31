<?php

declare(strict_types=1);

namespace App\Auth\DomainService;

use App\Auth\DomainService\Exception\InvalidCredentialsException;
use App\Models\User;
use App\User\Enum\AccountStatus;
use App\User\Dto\ValueObject\EmailAddress;
use App\User\Dto\ValueObject\PlainPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

final readonly class LoginService
{
    public function handle(EmailAddress $email, PlainPassword $password, bool $remember = false): User
    {
        $candidate = User::query()->where('email', $email->value())->first();

        if ($candidate instanceof User && Hash::check($password->value(), (string) $candidate->password)) {
            if (!$candidate->hasActiveAccount()) {
                Auth::login($candidate, $remember);
                Session::regenerate();

                return $candidate;
            }
        }

        if (!Auth::attempt(['email' => $email->value(), 'password' => $password->value(), 'account_status' => AccountStatus::ACTIVE->value], $remember)) {
            throw InvalidCredentialsException::create();
        }

        Session::regenerate();

        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
