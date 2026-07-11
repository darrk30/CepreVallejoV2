<?php

namespace App\Providers;

use App\Listeners\CloseLoginHistoryOnLogout;
use App\Listeners\EnforceSingleSession;
use App\Listeners\RecordLoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
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
        Event::listen(Login::class, RecordLoginHistory::class);
        Event::listen(Login::class, EnforceSingleSession::class);
        Event::listen(Logout::class, CloseLoginHistoryOnLogout::class);
    }
}
