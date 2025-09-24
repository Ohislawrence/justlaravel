<?php

namespace App\Listeners;


use Illuminate\Auth\Events\Login as UserJustLoggedin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\UserLoginNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stevebauman\Location\Facades\Location;

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
            $ipAddress = request()->ip();
            
            if($ipAddress == '127.0.0.1')
            {
                $location = 'Home';
            }else{
                $currentUserInfo = Location::get($ipAddress);
                $country = $currentUserInfo->countryName;
                $city =$currentUserInfo->cityName;
                $location = $city.', '.$country;
            }
           
            
            // Get user agent
            $userAgent = request()->header('User-Agent');
            
            // Send notification
            Notification::send($user, new UserLoginNotification($location, $userAgent));
            
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