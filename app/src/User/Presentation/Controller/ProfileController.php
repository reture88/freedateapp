<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ProfileController
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $addresses = $user->addresses()->get()->keyBy('type');

        return view('user.profile', [
            'user' => $user,
            'shippingAddress' => $addresses->get(UserAddress::TYPE_SHIPPING),
            'billingAddress' => $addresses->get(UserAddress::TYPE_BILLING),
        ]);
    }
}
