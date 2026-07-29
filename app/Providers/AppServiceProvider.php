<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

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
    }
}
