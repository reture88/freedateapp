<?php

declare(strict_types=1);

namespace App\Legal\Presentation\Controller;

use Illuminate\Contracts\View\View;

final class ShowPrivacyController
{
    public function __invoke(): View
    {
        return view('legal.privacy');
    }
}
