<?php

declare(strict_types=1);

namespace App\Shared\DomainService\Bus\RequestHandler;

interface ValidateRequestInterface
{
    /**
     * @return array<string, string>
     */
    public static function rules(): array;
}
