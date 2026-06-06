<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StoreForgotPasswordController extends Controller
{
    /**
     * Show the form to request an OTP.
     */
    public function showForgotForm()
    {
        return view('store-forgot-password');
    }

    /**
     * Generate OTP and send via WhatsApp.
     */
    public function sendOtp(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->with('error', 'Nomor WhatsApp tidak ditemukan dalam sistem kami.');
        }

        // Generate a 6-digit OTP
        $otp = sprintf('%06d', mt_rand(100000, 999999));
        $expiresAt = Carbon::now()->addMinutes(5); // OTP valid for 5 minutes

        // Save OTP to database
        DB::table('whatsapp_otps')->updateOrInsert(
            ['phone' => $request->phone],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Send OTP via WhatsApp Service
        $message = "Halo {$user->name},\n\nKode OTP Anda untuk reset password Pharmacare adalah: *{$otp}*.\n\nKode ini berlaku selama 5 menit. JANGAN BERIKAN KODE INI KEPADA SIAPAPUN.";
        $sent = $waService->sendMessage($request->phone, $message);

        if ($sent) {
            session(['reset_phone' => $request->phone]); // Store phone in session for next step
            return redirect()->route('store.password.verify')->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
        } else {
            return back()->with('error', 'Gagal mengirim kode OTP ke WhatsApp. Silakan coba lagi.');
        }
    }

    /**
     * Show the form to input the OTP.
     */
    public function showVerifyForm()
    {
        if (!session('reset_phone')) {
            return redirect()->route('store.password.request');
        }
        return view('store-verify-otp');
    }

    /**
     * Verify the OTP.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $phone = session('reset_phone');
        if (!$phone) {
            return redirect()->route('store.password.request')->with('error', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        $record = DB::table('whatsapp_otps')->where('phone', $phone)->first();

        if (!$record) {
            return back()->with('error', 'Kode OTP tidak valid atau belum di-generate.');
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            return back()->with('error', 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.');
        }

        if ($record->otp !== $request->otp) {
            return back()->with('error', 'Kode OTP salah.');
        }

        // OTP is valid. Proceed to reset password.
        session(['otp_verified' => true]);
        return redirect()->route('store.password.reset.form')->with('success', 'Verifikasi berhasil. Silakan buat password baru.');
    }

    /**
     * Show the form to create a new password.
     */
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('reset_phone')) {
            return redirect()->route('store.password.request');
        }
        return view('store-reset-password');
    }

    /**
     * Update the user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->uncompromised()],
        ]);

        $phone = session('reset_phone');
        if (!session('otp_verified') || !$phone) {
            return redirect()->route('store.password.request')->with('error', 'Sesi tidak valid.');
        }

        $user = User::where('phone', $phone)->first();
        if ($user) {
            // Cek apakah password baru sama dengan password lama
            if (Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Password baru tidak boleh sama dengan password Anda saat ini.');
            }

            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Clean up database and session
        DB::table('whatsapp_otps')->where('phone', $phone)->delete();
        $request->session()->forget(['reset_phone', 'otp_verified']);

        return redirect()->route('store.login')->with('success', 'Password berhasil diubah. Silakan masuk dengan password baru Anda.');
    }
}
