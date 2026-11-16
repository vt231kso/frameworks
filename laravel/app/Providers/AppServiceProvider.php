<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Правила для перевірки ролей через middleware can:admin та can:manager[cite: 9, 10]
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('manager', fn (User $user) => $user->isManager());

        // Захист від підбору пароля: 5 спроб за 15 хвилин на пару email + IP[cite: 10]
        RateLimiter::for('login', fn (Request $request) => Limit::perMinutes(15, 5)
            ->by(mb_strtolower((string) $request->input('email')).'|'.$request->ip()));
    }
}
