<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Profile\DomainService\UpdateDatingProfileVisibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class UpdateVisibilityController
{
    public function __construct(private UpdateDatingProfileVisibilityService $service)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'visibility_status' => 'required|in:visible,hidden',
        ]);

        $this->service->handle($user, $validated['visibility_status']);

        return redirect('/dating-profile/preview')->with('success', 'Profile visibility updated.');
    }
}
