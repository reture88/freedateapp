<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Profile\DomainService\SaveDatingProfileOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class SaveOnboardingController
{
    public function __construct(private SaveDatingProfileOnboardingService $service)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'display_name' => 'required|string|max:80',
            'birthdate' => 'required|date|before_or_equal:-18 years',
            'gender' => 'required|string|max:40',
            'orientation' => 'required|string|max:40',
            'city' => 'required|string|max:120',
            'country_code' => 'required|string|size:2',
            'approximate_latitude' => 'nullable|numeric|between:-90,90',
            'approximate_longitude' => 'nullable|numeric|between:-180,180',
            'relationship_intent' => 'required|string|max:60',
            'bio' => 'nullable|string|max:1000',
            'ai_wingman_enabled' => 'nullable|boolean',
            'privacy_consent' => 'accepted',
        ]);

        $this->service->handle($user, $validated);

        return redirect('/dating-profile/preview')->with('success', 'Dating profile completed.');
    }
}
