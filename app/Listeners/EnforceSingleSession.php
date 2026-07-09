<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class EnforceSingleSession
{
    public function handle(Login $event): void
    {
        DB::table('sessions')
            ->where('user_id', $event->user->getAuthIdentifier())
            ->where('id', '!=', session()->getId())
            ->delete();
    }
}
