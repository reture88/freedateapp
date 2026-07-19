<?php

declare(strict_types=1);

namespace App\User\RequestHandler\UpdateProfile;

use App\Models\User;
use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\Dto\ValueObject\EmailAddress;
use App\User\Dto\ValueObject\Name;

final readonly class UpdateProfileCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public User $user,
        public Name $name,
        public EmailAddress $email,
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
            new Name((string) $payload['name']),
            new EmailAddress((string) $payload['email']),
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
        ];
    }
}
