<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', static function (Request $request): Limit {
            return Limit::perMinute(60)->by((string) ($request->user()?->id ?? $request->ip()));
        });

        $this->routes(function (): void {
            Route::middleware('web')
                ->group(base_path('routes/routes.php'))
            ;

            foreach ($this->domainRouteFiles() as $routeFile) {
                Route::middleware('web')->group($routeFile);
            }
        });
    }

    /**
     * @return list<string>
     */
    private function domainRouteFiles(): array
    {
        $routeFiles = glob(base_path('src/*/Presentation/Routes/routes.php')) ?: [];
        sort($routeFiles);

        return $routeFiles;
    }
}
