<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Translator;

final class Translator
{
    /**
     * @param array<string, bool|float|int|string> $replace
     */
    public function trans(string $key, ?string $locale = null, array $replace = []): string
    {
        return self::transStatic($key, $locale, $replace);
    }

    /**
     * @param array<string, bool|float|int|string> $replace
     */
    public static function transStatic(string $key, ?string $locale = null, array $replace = []): string
    {
        $translation = trans($key, $replace, $locale);

        return is_string($translation) ? $translation : '';
    }
}
