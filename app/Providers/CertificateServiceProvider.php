<?php

namespace App\Providers;

use App\Services\CertificateService;
use Illuminate\Support\ServiceProvider;

class CertificateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CertificateService::class, function ($app) {
            return new CertificateService();
        });
    }

    public function boot()
    {
        //
    }
}
