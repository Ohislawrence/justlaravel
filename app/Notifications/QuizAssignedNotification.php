<?php

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuizAssignedNotification extends Notification
{
    use Queueable;

     public $quiz;
    public $group;

    /**
     * Create a new notification instance.
     */
    public function __construct(Quiz $quiz, $group)
    {
        $this->quiz = $quiz;
        $this->group = $group;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Strip HTML tags from description
        $cleanDescription = strip_tags($this->quiz->description ?? 'No description provided');
        
        return (new MailMessage)
            ->subject('New Quiz Assigned: ' . $this->quiz->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new quiz has been assigned to your group: ' . $this->group)
            ->line('Quiz: ' . $this->quiz->title)
            ->line('Description: ' . $cleanDescription) 
            ->action('Take Quiz', route('quiz.show', ['quiz' => $this->quiz->id,'token'=> $this->quiz->share_token]))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        // Strip HTML tags from description for database storage too
        $cleanDescription = strip_tags($this->quiz->description ?? 'No description provided');
        
        return [
            'quiz_id' => $this->quiz->id,
            'quiz_title' => $this->quiz->title,
            'group_name' => $this->group,
            'quiz_description' => $cleanDescription, 
            'message' => 'A new quiz "' . $this->quiz->title . '" has been assigned to your group "' . $this->group . '"',
            'action_url' => route('quiz.show', ['quiz' => $this->quiz->id,'token'=> $this->quiz->share_token]),
        ];
    }
}
