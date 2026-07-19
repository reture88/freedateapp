<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Models\ProfileTagCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ShowOnboardingController
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('profile.onboarding.show', [
            'profile' => $user->datingProfile,
            'tagCategories' => ProfileTagCategory::query()->with('tags')->orderBy('sort_order')->get(),
        ]);
    }
}
