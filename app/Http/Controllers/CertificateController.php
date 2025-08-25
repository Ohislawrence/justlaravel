<?php

namespace App\Http\Controllers;


use App\Models\Certificate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = auth()->user()->certificates()
            ->with(['quiz', 'user'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Certificate/Index', [
            'certificates' => $certificates
        ]);
    }

    public function show(Certificate $certificate)
    {
        //$this->authorize('view', $certificate);

        $certificate->load(['quiz', 'user', 'template']);

        return Inertia::render('Certificate/Show', [
            'certificate' => $certificate
        ]);
    }

    public function download(Certificate $certificate)
    {
        //$this->authorize('view', $certificate);

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate
        ]);

        return $pdf->download("certificate_{$certificate->certificate_number}.pdf");
    }

    public function verify(Request $request)
    {
        $certificate = Certificate::find($request->id);

        if (!$certificate) {
            return Inertia::render('Certificate/Verify', [
                'valid' => false,
                'message' => 'Certificate not found'
            ]);
        }

        return Inertia::render('Certificate/Verify', [
            'valid' => true,
            'certificate' => $certificate->load(['quiz', 'user'])
        ]);
    }
}
