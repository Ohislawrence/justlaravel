<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_id',
        'title',
        'certificate_number',
        'content',
        'template',
        'qr_code',
        'issued_at',
        'expires_at',
        'metadata',
    ];
    

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class);
    }
    
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }
}
