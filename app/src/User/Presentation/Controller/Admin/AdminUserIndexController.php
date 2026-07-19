<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AdminUserIndexController
{
    public function __invoke(Request $request): View
    {
        $query = User::query()->latest();
        $term = trim((string) $request->query('q', ''));
        $role = (string) $request->query('role', '');
        $active = $request->query('is_active');

        if ('' !== $term) {
            $like = '%'.$term.'%';
            $query->where(static function ($query) use ($like): void {
                $query->where('name', 'like', $like)->orWhere('email', 'like', $like);
            });
        }

        if ('' !== $role) {
            $query->where('role', $role);
        }

        if (null !== $active && '' !== $active) {
            $query->where('is_active', (bool) $active);
        }

        return view('admin.users.index', [
            'users' => $query->paginate(25)->withQueryString(),
            'filters' => $request->query(),
        ]);
    }
}
