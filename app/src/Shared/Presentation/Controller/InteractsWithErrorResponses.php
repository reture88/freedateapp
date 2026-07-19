<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Controller;

use App\Shared\Infrastructure\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;

trait InteractsWithErrorResponses
{
    /**
     * @param array<string, mixed> $input
     */
    protected function backWithValidationErrors(ValidationException $exception, array $input = []): RedirectResponse
    {
        if ([] === $input) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return back()->withErrors($exception->errors())->withInput($input);
    }

    protected function backWithMessageError(string $key, string $message, bool $withInput = true): RedirectResponse
    {
        $response = back()->withErrors([$key => $message]);

        return $withInput ? $response->withInput() : $response;
    }
}
