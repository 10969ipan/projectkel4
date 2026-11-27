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
            'history' => 'nullable|array|max:12',
            'history.*.role' => 'required_with:history|string|in:user,assistant,model',
            'history.*.content' => 'required_with:history|string|max:2000',
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

        // Bangun riwayat percakapan multi-turn (dari frontend widget atau riwayat TelemedicineChat di DB)
        $rawHistory = $validated['history'] ?? [];
        if (empty($rawHistory) && Auth::check() && $request->filled('doctor_id')) {
            try {
                $rawHistory = TelemedicineChat::where('user_id', Auth::id())
                    ->where('doctor_id', $request->doctor_id)
                    ->orderBy('created_at', 'desc')
                    ->limit(8)
                    ->get()
                    ->reverse()
                    ->map(fn ($c) => [
                        'role' => $c->is_from_doctor ? 'assistant' : 'user',
                        'content' => (string) $c->message,
                    ])
                    ->values()
                    ->all();
            } catch (\Throwable $e) {
                $rawHistory = [];
            }
        }

        // Normalisasi urutan giliran percakapan (user <-> model/assistant) agar valid di Gemini & Groq
        $normalizedTurns = [];
        foreach (array_slice($rawHistory, -8) as $turn) {
            $role = ($turn['role'] ?? '') === 'user' ? 'user' : 'assistant';
            $text = mb_convert_encoding(trim((string) ($turn['content'] ?? '')), 'UTF-8', 'UTF-8');
            if ($text === '') {
                continue;
            }
            if (empty($normalizedTurns) && $role !== 'user') {
                continue; // Gemini mensyaratkan pesan pertama di contents harus 'user'
            }
            $lastIdx = count($normalizedTurns) - 1;
            if ($lastIdx >= 0 && $normalizedTurns[$lastIdx]['role'] === $role) {
                $normalizedTurns[$lastIdx]['content'] .= "\n" . $text;
            } else {
                $normalizedTurns[] = ['role' => $role, 'content' => $text];
            }
        }

        // Pastikan pesan terakhir sebelum pesan baru bukan 'user' menggantung
        if (!empty($normalizedTurns) && $normalizedTurns[count($normalizedTurns) - 1]['role'] === 'user') {
            $normalizedTurns[count($normalizedTurns) - 1]['content'] .= "\n" . $userMessage;
        } else {
            $normalizedTurns[] = ['role' => 'user', 'content' => $userMessage];
        }

        $hasHistory = count($normalizedTurns) > 1;

        $geminiContents = array_map(fn ($t) => [
            'role' => $t['role'] === 'user' ? 'user' : 'model',
            'parts' => [['text' => $t['content']]],
        ], $normalizedTurns);

        // Ambil maksimal 12 produk paling relevan (dengan pemetaan sinonim keluhan) agar token kecil (<350 token) & bebas antrean 503
        try {
            $msgLower = mb_strtolower($userMessage . ' ' . collect($normalizedTurns)->pluck('content')->join(' '));
            $synonymMap = [
                'perut'   => ['maag', 'lambung', 'kembung', 'diare', 'mencret', 'mual', 'angin', 'promag', 'antangin'],
                'maag'    => ['lambung', 'perih', 'ulu', 'kembung', 'promag'],
                'diare'   => ['mencret', 'bab', 'perut', 'oralit'],
                'kepala'  => ['pusing', 'nyeri', 'migrain', 'paracetamol'],
                'pusing'  => ['kepala', 'nyeri', 'angin', 'paracetamol'],
                'demam'   => ['panas', 'menggigil', 'paracetamol', 'sanmol'],
                'panas'   => ['demam', 'paracetamol', 'sanmol'],
                'batuk'   => ['flu', 'pilek', 'dahak', 'tenggorokan'],
                'flu'     => ['pilek', 'batuk', 'hidung', 'vitamin'],
                'gatal'   => ['kulit', 'alergi', 'jamur', 'panu', 'kurap', 'salep'],
                'luka'    => ['lecet', 'infeksi', 'antiseptik', 'betadine'],
                'lemas'   => ['vitamin', 'daya tahan', 'imun'],
            ];

            $searchTerms = collect(preg_split('/\s+/', mb_strtolower($userMessage)))
                ->map(fn ($w) => preg_replace('/[^\p{L}\p{N}]/u', '', $w))
                ->filter(fn ($w) => mb_strlen($w) >= 3)
                ->take(5)
                ->values()
                ->all();

            foreach ($synonymMap as $trigger => $extras) {
                if (str_contains($msgLower, $trigger)) {
                    $searchTerms = array_merge($searchTerms, $extras);
                }
            }
            $searchTerms = array_slice(array_values(array_unique($searchTerms)), 0, 12);

            $selectCols = ['id', 'name', 'price', 'stock', 'description', 'requires_prescription'];
            $matchedItems = collect();
            if (!empty($searchTerms)) {
                $matchedItems = Item::query()
                    ->select($selectCols)
                    ->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $kw) {
                            $q->orWhere('name', 'like', "%{$kw}%")
                              ->orWhere('description', 'like', "%{$kw}%");
                        }
                    })
                    ->limit(10)
                    ->get();
            }

            $remaining = 12 - $matchedItems->count();
            $generalItems = $remaining > 0
                ? Item::query()
                    ->select($selectCols)
                    ->when($matchedItems->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $matchedItems->pluck('id')))
                    ->limit($remaining)
                    ->get()
                : collect();

            $items = $matchedItems->concat($generalItems)
                ->map(function ($i) {
                    $cleanDesc = $i->description
                        ? Str::limit(preg_replace('/\s+/', ' ', trim(strip_tags($i->description))), 55)
                        : '';
                    $priceStr = $i->price ? 'Rp' . number_format((float) $i->price, 0, ',', '.') : '';
                    $stockStr = ((int) ($i->stock ?? 0)) > 0 ? 'Tersedia' : 'Habis';
                    $meta = implode(', ', array_filter([$priceStr, $stockStr, $i->requires_prescription ? 'Wajib Resep' : null]));
                    $line = "- {$i->name}" . ($meta ? " ({$meta})" : '') . ($cleanDesc ? ": {$cleanDesc}" : '');
                    return mb_convert_encoding($line, 'UTF-8', 'UTF-8');
                })
                ->join("\n");
        } catch (\Throwable $e) {
            Log::warning('Chatbot gagal memuat katalog produk: ' . $e->getMessage());
            $items = '(Data katalog sedang dimuat)';
        }

        $greetingRule = $hasHistory
            ? 'Ini lanjutan obrolan: JANGAN sapa ulang atau perkenalkan diri lagi, langsung tanggapi cerita pasien secara hangat.'
            : 'Sapa singkat dengan hangat dan akrab, tanpa kalimat pembuka kaku seperti CS robot.';

        $systemPrompt = <<<EOT
Kamu **SIMA**, apoteker virtual Pharmacare yang hangat, empatik, dan akrab seperti apoteker manusia sungguhan.
Aturan:
- Gunakan Bahasa Indonesia santai-sopan yang natural. Hindari disclaimer kaku berulang.
- {$greetingRule}
- Jika keluhan masih umum (misal sakit perut/batuk/pusing/demam), beri empati singkat lalu TANYA BALIK 1-2 hal singkat untuk menggali gejala (contoh sakit perut: perih di ulu hati seperti maag, begah/kembung, atau melilit disertai diare? Sejak kapan?).
- Sebutkan opsi obat relevan dari stok di bawah pakai **huruf tebal** & poin `-` singkat. Jika obat khusus belum ada di stok, sampaikan jujur & beri tips pertolongan pertama singkat. Ingatkan resep bila bertanda `Wajib Resep`, atau ke dokter bila gejala berat.
- Paragraf pendek, maksimal 130 kata.

Stok Pharmacare:
{$items}
EOT;

        $reply = null;
        $lastStatus = null;
        $startedAt = microtime(true);
        $curlOptions = defined('CURL_IPRESOLVE_V4')
            ? ['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]]
            : [];

        // 1. Prioritaskan Google Gemini API apabila GEMINI_API_KEY tersedia
        // Gunakan retry langsung pada gemini-3.5-flash-lite untuk mengatasi lonjakan 503 sesaat di Google AI Studio
        if ($geminiKey) {
            $configuredGeminiModel = trim((string) config('services.gemini.model'), " \t\n\r\0\x0B\"'");
            $primaryModel = ($configuredGeminiModel && !in_array($configuredGeminiModel, ['gemini-2.5-flash', 'gemini-2.5-flash-lite', 'gemini-1.5-flash'], true))
                ? $configuredGeminiModel
                : 'gemini-3.5-flash-lite';

            $attemptModels = [
                $primaryModel,
                'gemini-3.5-flash-lite',
                'gemini-2.0-flash-lite',
                'gemini-3.5-flash-lite',
            ];

            foreach ($attemptModels as $index => $geminiModel) {
                $elapsed = microtime(true) - $startedAt;
                if ($index > 0 && $elapsed > 7.2) {
                    break;
                }

                if ($index > 0) {
                    usleep(250000); // Jeda 250ms sebelum retry saat server Google sedang 503
                }

                $generationConfig = [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 650,
                ];

                if (str_contains($geminiModel, '3.5-flash-lite')) {
                    $generationConfig['thinkingConfig'] = ['thinkingLevel' => 'MINIMAL'];
                } elseif (str_starts_with($geminiModel, 'gemini-3')) {
                    $generationConfig['thinkingConfig'] = ['thinkingLevel' => 'LOW'];
                }
                // gemini-2.0-flash-lite: tidak support thinkingConfig, biarkan default

                $remainingTimeout = max(3, (int) floor(9.0 - (microtime(true) - $startedAt)));
                $perTryTimeout = ($index === 0) ? 4 : min(4, $remainingTimeout);

                try {
                    $geminiResponse = Http::withHeaders([
                            'Accept' => 'application/json',
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                        ])
                        ->withoutVerifying()
                        ->withOptions($curlOptions)
                        ->connectTimeout(3)
                        ->timeout($perTryTimeout)
                        ->post(
                            "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                            [
                                'system_instruction' => [
                                    'parts' => [['text' => $systemPrompt]],
                                ],
                                'contents' => $geminiContents,
                                'generationConfig' => $generationConfig,
                            ]
                        );

                    $lastStatus = $geminiResponse->status();

                    if ($geminiResponse->successful()) {
                        $responseJson = $geminiResponse->json();
                        $finishReason = $responseJson['candidates'][0]['finishReason'] ?? '';
                        $parts = $responseJson['candidates'][0]['content']['parts'] ?? [];
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
                        // Log jika model memberi 200 OK tapi output kosong (safety filter / OTHER)
                        Log::warning("Chatbot Gemini empty output [try#" . ($index + 1) . " {$geminiModel}] finishReason={$finishReason}");
                        // Jika bukan timeout / recitation, lanjut retry dengan model berbeda
                        if (in_array($finishReason, ['SAFETY', 'OTHER', ''], true) && $index < 2) {
                            usleep(300000);
                        }
                    } else {
                        $errBody = $geminiResponse->body();
                        Log::error("Chatbot Gemini Error [try#" . ($index + 1) . " {$geminiModel}] ({$lastStatus}): " . Str::limit($errBody, 500));

                        // Hentikan retry jika API key tidak valid / diblokir permanen
                        if (in_array($lastStatus, [400, 401, 403], true) && (
                            str_contains($errBody, 'API_KEY_INVALID') ||
                            str_contains($errBody, 'PERMISSION_DENIED') ||
                            str_contains($errBody, 'API_KEY_HTTP_REFERRER_BLOCKED')
                        )) {
                            $lastStatus = 401;
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Chatbot Gemini Exception [try#" . ($index + 1) . " {$geminiModel}]: " . $e->getMessage());
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
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $normalizedTurns
                    ),
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

                    if ($lastStatus === null) {
                        $lastStatus = $response->status();
                    }
                    $groqStatus = $response->status();

                    if ($response->successful()) {
                        $content = (string) ($response->json('choices.0.message.content') ?? '');
                        $content = trim(preg_replace('/<think>[\s\S]*?<\/think>/i', '', $content));

                        if ($content !== '') {
                            $reply = $content;
                            break;
                        }
                    } else {
                        Log::error("Chatbot Groq Error [{$model}] ({$groqStatus}): " . Str::limit($response->body(), 500));

                        if (in_array($groqStatus, [401, 403], true)) {
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Chatbot Groq Exception [{$model}]: " . $e->getMessage());
                    break;
                }
            }
        }

        $retryable = false;
        if ($reply === null) {
            if ($lastStatus === 401) {
                $reply = 'Maaf, kunci akses layanan AI tidak valid atau kedaluwarsa. Silakan periksa konfigurasi API Key.';
            } elseif ($lastStatus === null) {
                $retryable = true;
                $reply = 'Gagal terhubung dengan pusat AI. Silakan periksa koneksi internet Anda atau coba lagi nanti.';
            } else {
                $retryable = true;
                $reply = 'Maaf, layanan sedang sibuk. Silakan coba kembali dalam beberapa saat.';
            }
        }

        // SIMPAN HISTORY (Hanya jika berhasil mendapat balasan valid, ada doctor_id, dan user login)
        if (!$retryable && $lastStatus !== 401 && Auth::check() && $request->filled('doctor_id')) {
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

        return response()->json([
            'reply' => $reply,
            'retryable' => $retryable,
        ]);
    }
}
