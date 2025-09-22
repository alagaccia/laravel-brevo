<?php

namespace AndreaLagaccia\Brevo;

use Illuminate\Support\ServiceProvider;

class BrevoServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/brevo.php' => config_path('brevo.php'),
        ]);
    }
}
