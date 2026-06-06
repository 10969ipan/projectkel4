<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Services\WhatsAppService;
use Carbon\Carbon;

class SendLoginNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Fitur WhatsApp Security Alert dinonaktifkan atas permintaan pengguna.
        // Jika ingin diaktifkan kembali, hapus komentar di bawah ini:
        
        /*
        $user = $event->user;

        if ($user && $user->phone) {
            $waService = app(WhatsAppService::class);
            $time = Carbon::now()->format('d M Y, H:i');
            $ip = request()->ip();

            $message = "🚨 *Security Alert Pharmacare* 🚨\n\nHalo {$user->name},\n\nAkun Anda baru saja login.\n\nWaktu: {$time}\nIP: {$ip}\n\nJika ini BUKAN Anda, segera ubah password akun Anda demi keamanan!";
            
            // Only fire if we are not running in console/seeder to avoid spam
            if (!app()->runningInConsole()) {
                $waService->sendMessage($user->phone, $message);
            }
        }
        */
    }
}
