<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examinee extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $guard = 'examinee';

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'unique_code',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'user_id');
    }

    // Generate a unique code
    public static function generateUniqueCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('unique_code', $code)->exists());

        return $code;
    }
}
