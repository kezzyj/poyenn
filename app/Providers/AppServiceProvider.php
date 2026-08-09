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
                if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        \View::composer(['shop.partials.header', 'shop.partials.mobile-menu'], function ($view) {
            $platform = \App\Models\Platform::where('slug', 'poyenn')->first();

            $navCategories = \Illuminate\Support\Facades\Cache::remember('nav_categories_' . ($platform?->id ?? 1), 3600, function () use ($platform) {
                if (!$platform) return collect();

                return \App\Models\Category::where('platform_id', $platform->id)
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(8)
                    ->get();
            });

            $view->with('navCategories', $navCategories);
        });

        // NEW — Cart count for header badge
        \View::composer('shop.partials.header', function ($view) {
            $cartCount = 0;
            if (auth()->guard('web')->check()) {
                $cartCount = app(\App\Services\CartService::class)->getCartCount();
            }
            $view->with('cartCount', $cartCount);
        });
     }
}
