<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use Illuminate\Support\Str;

class CertificateService
{

    public function __construct()
    {
        //
    }
    public function generateCertificate(QuizAttempt $attempt): ?Certificate
    {
        $quiz = $attempt->quiz;
        
        if (!$quiz->enable_certificates || 
            $attempt->percentage < $quiz->certificate_pass_percentage) {
            return null;
        }

        $template = $quiz->certificateTemplate ?? CertificateTemplate::default()->first();
        
        if (!$template) {
            return null;
        }

        return Certificate::create([
            'quiz_id' => $quiz->id,
            'user_id' => $attempt->user_id,
            'attempt_id' => $attempt->id,
            'title' => "Certificate of Completion: {$quiz->title}",
            'certificate_number' => Str::uuid(),
            'content' => $this->generateContent($template, $attempt),
            'template' => $template->content,
            'issued_at' => now(),
            'expires_at' => $quiz->certificate_expiry_days 
                ? now()->addDays($quiz->certificate_expiry_days) 
                : null,
            'metadata' => [
                'score' => $attempt->percentage,
                'quiz_title' => $quiz->title,
                'user_name' => $attempt->user->name,
            ]
        ]);
    }

    protected function generateContent($template, $attempt)
    {
        // Implement your template parsing logic here
        return str_replace(
            ['{{user}}', '{{score}}', '{{date}}', '{{quiz}}'],
            [
                $attempt->user->name,
                $attempt->percentage,
                now()->format('F j, Y'),
                $attempt->quiz->title
            ],
            $template->content
        );
    }
}