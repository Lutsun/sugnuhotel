<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Certaines configs PHP locales (ex. XAMPP) fixent serialize_precision à une valeur
        // élevée, ce qui fait apparaître les floats JSON en pleine précision binaire (ex.
        // 23.910000000000000142...). On force la représentation la plus courte et exacte.
        ini_set('serialize_precision', -1);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Le frontend Angular gère l'écran de réinitialisation, le lien de l'email doit y pointer.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return rtrim(config('app.frontend_url'), '/')
                .'/reset-password?token='.$token
                .'&email='.urlencode($notifiable->getEmailForPasswordReset());
        });

        // Envoi via l'API HTTP de Brevo plutôt que le SMTP : certains hébergeurs (Railway...)
        // bloquent les ports SMTP sortants, mais laissent passer le HTTPS.
        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory())->create(
                new Dsn('brevo+api', 'default', config('services.brevo.key'))
            );
        });
    }
}
