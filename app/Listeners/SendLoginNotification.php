<?php

namespace App\Listeners;

use App\Mail\LoginAlertEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLoginNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $ip = request()->ip();
        $time = now()->toDayDateTimeString();

        Mail::to($user->email)->queue(new LoginAlertEmail($user, $ip, $time));
    }
}
