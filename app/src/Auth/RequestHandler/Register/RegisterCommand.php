<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Register;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\Dto\ValueObject\EmailAddress;
use App\User\Dto\ValueObject\Name;
use App\User\Dto\ValueObject\PlainPassword;

final readonly class RegisterCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public Name $name,
        public EmailAddress $email,
        public PlainPassword $password,
        public PlainPassword $passwordConfirmation,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            new Name((string) $payload['name']),
            new EmailAddress((string) $payload['email']),
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
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required',
        ];
    }
}
