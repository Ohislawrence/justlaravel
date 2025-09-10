<?php

namespace App\Listeners;


use Illuminate\Auth\Events\Login as UserJustLoggedin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\UserLoginNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendLoginNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserJustLoggedin $event): void
    {
        try {
            $user = $event->user;
            $request = request();
            
            // Get client IP address (handling proxies)
            $ipAddress = $request->getClientIp();
            
            // Get user agent
            $userAgent = $request->userAgent();
            
            // Send notification
            Notification::send($user, new UserLoginNotification($ipAddress, $userAgent));
            
            // Optional: Log the login
            Log::info('Login notification sent to user: ' . $user->email, [
                'ip' => $ipAddress,
                'user_agent' => $userAgent
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send login notification: ' . $e->getMessage());
        }
    }
}