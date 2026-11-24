<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TelemedicineChat;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    /**
     * Tampilkan daftar dokter (Direktori)
     */
    public function index()
    {
        // Cari user yang rolenya doctor (atau fiktif)
        $doctors = User::where('store_role', 'doctor')->get();
        return view('frontend.telemedicine.index', compact('doctors'));
    }

    /**
     * Tampilkan ruang obrolan dengan dokter tertentu
     */
    public function chat($doctorId)
    {
        $doctor = User::findOrFail($doctorId);
        $chats = TelemedicineChat::where('user_id', Auth::id())
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        return view('frontend.telemedicine.chat', compact('doctor', 'chats'));
    }

    /**
     * Simpan pesan dari pasien
     */
    public function storeMessage(Request $request, $doctorId)
    {
        TelemedicineChat::create([
            'user_id' => Auth::id(),
            'doctor_id' => $doctorId,
            'message' => $request->message,
            'is_from_doctor' => false,
        ]);

        return redirect()->back();
    }

    /**
     * Aksi Dummy: Dokter menyetujui resep
     * (Dalam kasus nyata, ini diakses dari sisi panel Admin/Dokter)
     */
    public function approvePrescription($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_prescription_approved = true;
        $user->save();

        return redirect()->back()->with('success', 'Resep berhasil dikeluarkan untuk pasien.');
    }

    /**
     * AI Auto-Reply menggunakan Groq API (dengan fallback model & Gemini)
     */
    public function aiReply(Request $request)
    {
        Log::info('AI Bot hit: ' . $request->message);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'doctor_id' => 'nullable|exists:users,id',
        ]);

        $userMessage = mb_convert_encoding(trim($validated['message']), 'UTF-8', 'UTF-8');
        $groqKey = trim((string) config('services.groq.api_key'), " \t\n\r\0\x0B\"'");
        $geminiKey = trim((string) config('services.gemini.api_key'), " \t\n\r\0\x0B\"'");

        // Deteksi otomatis jika user menaruh key Gemini (AQ. / AIza) di variabel GROQ_API_KEY pada Vercel
        if ($groqKey && (str_starts_with($groqKey, 'AQ.') || str_starts_with($groqKey, 'AIza'))) {
            if (!$geminiKey) {
                $geminiKey = $groqKey;
            }
            $groqKey = '';
        }

        if (!$geminiKey && !$groqKey) {
            return response()->json(['reply' => 'Maaf, layanan AI sedang tidak tersedia.']);
        }

        // Ambil daftar obat dari database secara ringkas agar tidak melampaui batas token / timeout Vercel
        try {
            $items = Item::query()
                ->select('id', 'name', 'description', 'requires_prescription')
                ->limit(50)
                ->get()
                ->map(function ($i) {
                    $cleanDesc = $i->description
                        ? Str::limit(preg_replace('/\s+/', ' ', trim(strip_tags($i->description))), 120)
                        : '';
                    $line = "- {$i->name}"
                        . ($i->requires_prescription ? ' [RESEP WAJIB]' : '')
                        . ($cleanDesc ? ": {$cleanDesc}" : '');
                    return mb_convert_encoding($line, 'UTF-8', 'UTF-8');
                })
                ->join("\n");
        } catch (\Throwable $e) {
            Log::warning('Chatbot gagal memuat katalog produk: ' . $e->getMessage());
            $items = '(Data katalog sedang dimuat)';
        }

        $systemPrompt = <<<EOT
Kamu adalah **SIMA**, asisten pintar dan apoteker profesional dari Pharmacare.
Tugas Anda adalah memberikan konsultasi kefarmasian secara formal, akurat, dan sangat sopan dalam Bahasa Indonesia.

Panduan respons:
1. Berikan informasi yang valid: kegunaan berbasis farmakologi, dosis lazim, kontraindikasi, dan cara penyimpanan.
2. Jika obat termasuk kategori obat keras atau memerlukan resep dokter, Anda WAJIB memberikan peringatan secara formal.
3. Untuk keluhan yang bersifat krusial, arahkan pasien untuk segera mendapatkan penanganan medis di fasilitas kesehatan terdekat.
4. Rekomendasikan produk yang tersedia pada data inventaris secara objektif.
5. Gunakan tata bahasa yang baku, sopan (menggunakan "Anda" dan "Saya"), serta penjelasan yang sistematis.
6. Hindari memberikan diagnosis final; selalu tegaskan bahwa informasi ini bersifat edukatif dan konsultasi medis langsung tetap diperlukan.

Daftar produk yang tersedia di Pharmacare:
{$items}
EOT;

        $reply = null;
        $lastStatus = null;
        $startedAt = microtime(true);

        // 1. Prioritaskan Google Gemini API apabila GEMINI_API_KEY tersedia
        if ($geminiKey) {
            $configuredGeminiModel = trim((string) config('services.gemini.model'), " \t\n\r\0\x0B\"'");
            $geminiModels = array_values(array_unique(array_filter([
                $configuredGeminiModel ?: 'gemini-3.5-flash-lite',
                'gemini-3.5-flash-lite',
                'gemini-3.8-flash',
                'gemini-3.7-flash',
            ])));

            foreach ($geminiModels as $index => $geminiModel) {
                if ($index > 0 && (microtime(true) - $startedAt) > 5.5) {
                    break;
                }

                try {
                    $geminiResponse = Http::withHeaders([
                            'Accept' => 'application/json',
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                        ])
                        ->when(app()->isLocal(), fn ($http) => $http->withoutVerifying())
                        ->connectTimeout(3)
                        ->timeout(6)
                        ->post(
                            "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                            [
                                'system_instruction' => [
                                    'parts' => [['text' => $systemPrompt]],
                                ],
                                'contents' => [
                                    ['role' => 'user', 'parts' => [['text' => $userMessage]]],
                                ],
                                'generationConfig' => [
                                    'temperature' => 0.7,
                                    'maxOutputTokens' => 1024,
                                ],
                            ]
                        );

                    $lastStatus = $geminiResponse->status();

                    if ($geminiResponse->successful()) {
                        $parts = $geminiResponse->json('candidates.0.content.parts') ?? [];
                        $combined = '';
                        foreach ($parts as $part) {
                            if (!empty($part['thought'])) {
                                continue;
                            }
                            $combined .= $part['text'] ?? '';
                        }
                        $combined = trim($combined);
                        if ($combined !== '') {
                            $reply = $combined;
                            break;
                        }
                    } else {
                        Log::error("Chatbot Gemini Error [{$geminiModel}] ({$lastStatus}): " . Str::limit($geminiResponse->body(), 500));

                        if (in_array($lastStatus, [400, 401, 403], true) && str_contains($geminiResponse->body(), 'API_KEY_INVALID')) {
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Chatbot Gemini Exception [{$geminiModel}]: " . $e->getMessage());
                }
            }
        }

        // 2. Fallback ke Groq API apabila Gemini belum menghasilkan balasan dan GROQ_API_KEY tersedia
        if ($reply === null && $groqKey && (microtime(true) - $startedAt) < 6.0) {
            $configuredModel = trim((string) config('services.groq.model'), " \t\n\r\0\x0B\"'");
            $models = array_values(array_unique(array_filter([
                $configuredModel ?: 'openai/gpt-oss-120b',
                'openai/gpt-oss-20b',
                'llama-3.3-70b-versatile',
                'llama-3.1-8b-instant',
            ])));

            foreach ($models as $index => $model) {
                if ($index > 0 && (microtime(true) - $startedAt) > 6.5) {
                    break;
                }

                $payload = [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                    'top_p' => 1,
                ];

                if (str_starts_with($model, 'openai/gpt-oss-')) {
                    $payload['max_completion_tokens'] = 2048;
                    $payload['reasoning_effort'] = 'low';
                } else {
                    $payload['max_tokens'] = 1024;
                }

                try {
                    $response = Http::withToken($groqKey)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                        ])
                        ->when(app()->isLocal(), fn ($http) => $http->withoutVerifying())
                        ->connectTimeout(3)
                        ->timeout(6)
                        ->post('https://api.groq.com/openai/v1/chat/completions', $payload);

                    $lastStatus = $response->status();

                    if ($response->successful()) {
                        $content = (string) ($response->json('choices.0.message.content') ?? '');
                        $content = trim(preg_replace('/<think>[\s\S]*?<\/think>/i', '', $content));

                        if ($content !== '') {
                            $reply = $content;
                            break;
                        }
                    } else {
                        Log::error("Chatbot Groq Error [{$model}] ({$lastStatus}): " . Str::limit($response->body(), 500));

                        if (in_array($lastStatus, [401, 403], true)) {
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Chatbot Groq Exception [{$model}]: " . $e->getMessage());
                    break;
                }
            }
        }

        if ($reply === null) {
            if ($lastStatus === 401) {
                $reply = 'Maaf, kunci akses layanan AI tidak valid atau kedaluwarsa. Silakan periksa konfigurasi API Key.';
            } elseif ($lastStatus === null) {
                $reply = 'Gagal terhubung dengan pusat AI. Silakan periksa koneksi internet Anda atau coba lagi nanti.';
            } else {
                $reply = 'Maaf, layanan sedang sibuk. Silakan coba kembali dalam beberapa saat.';
            }
        }

        // SIMPAN HISTORY (Hanya jika ada doctor_id dan user login)
        if (Auth::check() && $request->filled('doctor_id')) {
            try {
                TelemedicineChat::create([
                    'user_id'        => Auth::id(),
                    'doctor_id'      => $request->doctor_id,
                    'message'        => $userMessage,
                    'is_from_doctor' => false,
                ]);
                TelemedicineChat::create([
                    'user_id'        => Auth::id(),
                    'doctor_id'      => $request->doctor_id,
                    'message'        => $reply,
                    'is_from_doctor' => true,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Gagal menyimpan riwayat chat AI: ' . $e->getMessage());
            }
        }

        return response()->json(['reply' => $reply]);
    }
}
