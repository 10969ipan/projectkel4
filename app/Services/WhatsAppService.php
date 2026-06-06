<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using Fonnte API.
     *
     * @param string $phone The recipient phone number (e.g. 081234567890)
     * @param string $message The message text to send
     * @return bool True if successfully queued/sent, false otherwise
     */
    public function sendMessage(string $phone, string $message): bool
    {
        $token = env('FONNTE_TOKEN', 'YOUR_FONNTE_TOKEN_HERE'); // Retrieve token from .env

        // Clean and format phone number to start with '62'
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == true) {
                Log::info("WhatsApp message sent successfully to {$phone} via Fonnte.");
                return true;
            }

            Log::error("Failed to send WhatsApp message via Fonnte to {$phone}. Error: " . json_encode($result));
            return false;

        } catch (\Exception $e) {
            Log::error("Exception when sending WhatsApp message to {$phone}: " . $e->getMessage());
            return false;
        }
    }
}
