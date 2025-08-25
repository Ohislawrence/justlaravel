<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        body { margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; }
        .certificate { 
            width: 297mm;
            height: 210mm;
            position: relative;
            background-image: url('{{ $certificate->template->settings['background_image'] ? 
                                  storage_path('app/public/' . $certificate->template->settings['background_image']) : '' }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .content {
            position: relative;
            z-index: 10;
            height: 100%;
            padding: 20mm;
        }
        .verification {
            position: absolute;
            bottom: 10mm;
            right: 10mm;
            font-size: 8pt;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="content">
            {!! $certificate->content !!}
            
            <div class="verification">
                <p>Certificate ID: {{ $certificate->certificate_number }}</p>
                <p>Verify at: {{ route('certificates.verify') }}?id={{ $certificate->id }}</p>
            </div>
        </div>
    </div>
</body>
</html>