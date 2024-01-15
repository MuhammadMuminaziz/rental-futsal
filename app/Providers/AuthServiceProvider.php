<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Notifications\NewMessage;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->greeting("Hallo " . $notifiable->name)
                ->subject('Verifikasi Email')
                ->line('Selamat datang di Rental Futsal! Terimakasih sudah melakukan verifikasi alamat email Anda di Rental Futsal, verifikasi dibutuhkan agar akun Anda dapat memiliki akses penuh pada aplikasi Rental Futsal')
                ->line('Klik link/tombol dibawah untuk melengkapi proses verifikasi email Anda. Mohon abaikan email ini jika Anda merasa tidak melakukan verifikasi email.')
                ->action('Klik untuk Verifikasi Email', $url)
                ->line('Jika ada pertanyaan silahkan hubungi kontak kami di support@rental.com');
        });
    }
}
