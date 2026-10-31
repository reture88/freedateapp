<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Admin;

use App\Models\User;
use App\User\Enum\AccountStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AdminUserIndexController
{
    public function __invoke(Request $request): View
    {
        $query = User::query()->latest();
        $term = trim((string) $request->query('q', ''));
        $role = (string) $request->query('role', '');
        $status = (string) $request->query('account_status', '');

        if ('' !== $term) {
            $like = '%'.$term.'%';
            $query->where(static function ($query) use ($like): void {
                $query->where('name', 'like', $like)->orWhere('email', 'like', $like);
            });
        }

        if ('' !== $role) {
            $query->where('role', $role);
        }

        if ('' !== $status) {
            $query->where('account_status', $status);
        }

        return view('admin.users.index', [
            'users' => $query->paginate(25)->withQueryString(),
            'filters' => $request->query(),
            'accountStatuses' => AccountStatus::cases(),
        ]);
    }
}
