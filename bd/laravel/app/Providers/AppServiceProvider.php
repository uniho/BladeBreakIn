<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ※
        $this->app[\Illuminate\Contracts\Http\Kernel::class]->
            pushMiddleware(InjectDebugbarColector::class);
 
        \HQ::onBoot(); // ※
    }
}

// ※
class InjectDebugbarColector
{
    public function handle($request, $next)
    {
        $response = $next($request);

        if (debugbar()->isEnabled() && !config('debugbar.inject', true)
            && $request->segment(1) != config('debugbar.route_prefix'))
        {
            debugbar()->collect();
        }

        return $response;
    }
}
