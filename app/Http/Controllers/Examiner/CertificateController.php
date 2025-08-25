<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Services\CertificateGenerator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function download(QuizAttempt $attempt)
    {
        // Authorization check - only allow download if user owns the certificate
        if ($attempt->user_id !== auth()->id() && !auth()->user()->isExaminer()) {
            abort(403);
        }

        // Ensure certificate exists and is passed
        if (!$attempt->is_passed || !$attempt->certificate) {
            abort(404, 'Certificate not available');
        }

        $pdf = app(CertificateGenerator::class)->generatePdf($attempt->certificate);

        return new StreamedResponse(
            function () use ($pdf) {
                echo $pdf;
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="certificate_'.$attempt->id.'.pdf"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
            ]
        );
    }
}
