<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use App\Models\AccessLog;
use App\Models\Subscription;
use Illuminate\Support\Facades\Session;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $provider = Session::get('provider');
        Session::forget('provider');

        AccessLog::create([
            'user_id' => $event->user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'provider' => $provider,
        ]);

        /*Subscription::firstOrCreate(
            ['user_id' => $event->user->id]
            //['trial_expiration_date' => now()->addDays(3)]
        );*/
    }
}
