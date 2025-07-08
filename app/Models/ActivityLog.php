<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'type',
        'description',
        'data',
        'subject_id',
        'subject_type',
        'causer_id',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public static function log(string $type, $subject = null, array $data = [], $causer = null)
    {
        $log = new static([
            'type' => $type,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        if ($subject) {
            $log->subject()->associate($subject);
        }

        if ($causer) {
            $log->causer()->associate($causer);
        } elseif (auth()->check()) {
            $log->causer()->associate(auth()->user());
        }

        $log->save();

        return $log;
    }
}
