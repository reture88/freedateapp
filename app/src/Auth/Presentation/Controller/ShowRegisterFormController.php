<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use Illuminate\Contracts\View\View;

final class ShowRegisterFormController
{
    public function __invoke(): View
    {
        return view('auth.register');
    }
}
