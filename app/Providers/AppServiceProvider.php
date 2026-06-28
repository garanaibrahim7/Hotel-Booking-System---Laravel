<?php

namespace App\Providers;

use App\Contracts\PaymentProviderInterface;
use App\Contracts\SubscriptionProviderInterface;
use App\Models\Discount;
use App\Observers\DiscountObserver;
use App\Services\Payments\StripeProvider;
use App\Services\Subscription\StripeSubscriptionProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PaymentProviderInterface::class,
            StripeProvider::class
        );
        $this->app->bind(
            SubscriptionProviderInterface::class,
            StripeSubscriptionProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        RateLimiter::for('strict_api', function (Request $request) {
            return Limit::perSecond(2)->by($request->ip());
        });

        Gate::define('manager-access', function ($user) {
            return $user->role === 'manager';
        });

        Gate::define('admin-access', function ($user) {
            return $user->role === 'admin';
        });

        Discount::observe(DiscountObserver::class);

        Paginator::useBootstrapFive();
        DB::listen(function ($query) {
            Log::channel('queries')->info('Query Executed : '.$query->sql);
        });

        View::composer('layouts.navbar', function ($view) {
            $role = Auth::user()->role ?? 'guest';

            $menulinks = config("menu.$role.navbar");
            $view->with('menulinks', $menulinks);
        });

        View::composer('layouts.sidebar', function ($view) {
            $role = Auth::user()->role ?? 'guest';

            $links = config("menu.$role.sidebar");
            $view->with('sidebarlinks', $links);
        });
    }
}
