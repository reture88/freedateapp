<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Login;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\Dto\ValueObject\EmailAddress;
use App\User\Dto\ValueObject\PlainPassword;

final readonly class LoginCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public EmailAddress $email,
        public PlainPassword $password,
        public bool $remember = false,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            new EmailAddress((string) $payload['email']),
            new PlainPassword((string) $payload['password']),
            filter_var($payload['remember'] ?? false, FILTER_VALIDATE_BOOL),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
}
