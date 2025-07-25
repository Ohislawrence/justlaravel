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
        Model $causer = null,
        int $organizationId = null
    ): ActivityLog {
        return ActivityLog::create([
            'type' => $type,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer ? $causer->id : null,
            'data' => json_encode($data),
            'organization_id' => $organizationId ?? self::resolveOrganizationId($subject, $causer)
        ]);
    }

    protected static function resolveOrganizationId(?Model $subject, ?Model $causer): ?int
    {
        // Try to get organization from subject
        if ($subject && method_exists($subject, 'organization')) {
            return $subject->organization_id ?? $subject->organization?->id;
        }

        // Try to get organization from causer
        if ($causer && method_exists($causer, 'currentOrganization')) {
            return $causer->organizations()->first()?->id;
        }

        return null;
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
            $user,
            $quiz->organization_id ?? $user?->organizations()->first()?->id
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
            $user ?? $attempt->user,
            $attempt->quiz->organization_id ?? ($user ?? $attempt->user)?->organizations()->first()?->id
        );
    }
}
