<?php

namespace App\Services;

use App\Models\Certificate;
use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateGenerator
{
    public function generatePdf(Certificate $certificate): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = view('certificates.template', [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'quiz' => $certificate->quiz,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
}