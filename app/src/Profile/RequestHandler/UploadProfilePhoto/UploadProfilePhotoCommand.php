<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\UploadProfilePhoto;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use Illuminate\Http\UploadedFile;

final readonly class UploadProfilePhotoCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $userId,
        public UploadedFile $photo,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self((int) $payload['user_id'], $payload['photo']);
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'user_id' => 'required',
            'photo' => 'required|file|image|max_file_kb:5120',
        ];
    }
}
