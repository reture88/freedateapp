<?php

declare(strict_types=1);

namespace App\User\RequestHandler\ChangePassword;

use App\Models\User;
use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\Dto\ValueObject\PlainPassword;

final readonly class ChangePasswordCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public User $user,
        public PlainPassword $currentPassword,
        public PlainPassword $password,
        public PlainPassword $passwordConfirmation,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        if (!$payload['user'] instanceof User) {
            throw new \InvalidArgumentException('Authenticated user is required.');
        }

        return new self(
            $payload['user'],
            new PlainPassword((string) $payload['current_password']),
            new PlainPassword((string) $payload['password']),
            new PlainPassword((string) $payload['password_confirmation']),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'current_password' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required',
        ];
    }
}
