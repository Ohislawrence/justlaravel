<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function log(
        string $type,
        Model $subject = null,
        array $data = [],
        Model $causer = null
    ): ActivityLog {
        return ActivityLog::log($type, $subject, $data, $causer);
    }

    public static function logQuizCreated($quiz, $user = null)
    {
        return self::log(
            'quiz_created',
            $quiz,
            [
                'quiz_title' => $quiz->title,
                'quiz_id' => $quiz->id,
                'creator_name' => $user?->name ?? 'System'
            ],
            $user
        );
    }

    public static function logQuizCompleted($attempt, $user = null)
    {
        return self::log(
            'quiz_completed',
            $attempt->quiz,
            [
                'quiz_title' => $attempt->quiz->title,
                'quiz_id' => $attempt->quiz->id,
                'user_name' => $attempt->user->name,
                'user_id' => $attempt->user->id,
                'percentage' => $attempt->percentage,
                'is_passed' => $attempt->is_passed,
                'attempt_id' => $attempt->id
            ],
            $user ?? $attempt->user
        );
    }
}
