<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UserLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ipAddress;
    public $userAgent;
    public $loginTime;

    /**
     * Create a new notification instance.
     */
    public function __construct($ipAddress, $userAgent)
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->loginTime = Carbon::now();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // You can add 'database' if you want to store notifications in DB
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Login Detected on Your Account')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new login was detected on your account:')
            ->line('**Time:** ' . $this->loginTime->format('F j, Y \a\t g:i A'))
            ->line('**IP Address:** ' . $this->ipAddress)
            ->line('**Device/Browser:** ' . $this->userAgent)
            ->line('If this was you, you can ignore this message.')
            ->line('If you don\'t recognize this activity, please secure your account immediately.')
            ->action('Secure Your Account', route('profile.show'))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'login_time' => $this->loginTime,
            'message' => 'New login detected from IP: ' . $this->ipAddress,
        ];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        // Check if user wants to receive login notifications
        if (method_exists($notifiable, 'receivesLoginNotifications')) {
            return $notifiable->receivesLoginNotifications();
        }
        
        return true; // Default to sending if method doesn't exist
    }
}