<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use App\Auth\DomainService\AuthDestinationService;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class HomeController
{
    public function __construct(private AuthDestinationService $destinationService)
    {
    }

    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->user() instanceof User) {
            return redirect()->route($this->destinationService->routeNameFor($request->user()));
        }

        return view('home');
    }
}
