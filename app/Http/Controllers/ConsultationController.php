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

        // Ambil katalog produk Pharmacare (prioritaskan kata kunci relevan + sisa katalog hingga 60 item lengkap dengan harga & stok)
        try {
            $keywords = collect(preg_split('/\s+/', mb_strtolower($userMessage)))
                ->map(fn ($w) => preg_replace('/[^\p{L}\p{N}]/u', '', $w))
                ->filter(fn ($w) => mb_strlen($w) >= 3)
                ->take(5)
                ->values();

            $selectCols = ['id', 'name', 'price', 'stock', 'description', 'requires_prescription'];
            $matchedItems = collect();
            if ($keywords->isNotEmpty()) {
                $matchedItems = Item::query()
                    ->select($selectCols)
                    ->where(function ($q) use ($keywords) {
                        foreach ($keywords as $kw) {
                            $q->orWhere('name', 'like', "%{$kw}%")
                              ->orWhere('description', 'like', "%{$kw}%");
                        }
                    })
                    ->limit(20)
                    ->get();
            }

            $remaining = 60 - $matchedItems->count();
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
                        ? Str::limit(preg_replace('/\s+/', ' ', trim(strip_tags($i->description))), 85)
                        : '';
                    $priceStr = $i->price ? 'Rp' . number_format((float) $i->price, 0, ',', '.') : '';
                    $stockStr = ((int) ($i->stock ?? 0)) > 0 ? "Stok: {$i->stock}" : 'Stok habis';
                    $meta = implode(', ', array_filter([$priceStr, $stockStr, $i->requires_prescription ? 'WAJIB RESEP DOKTER' : null]));
                    $line = "- {$i->name}" . ($meta ? " ({$meta})" : '') . ($cleanDesc ? ": {$cleanDesc}" : '');
                    return mb_convert_encoding($line, 'UTF-8', 'UTF-8');
                })
                ->join("\n");
        } catch (\Throwable $e) {
            Log::warning('Chatbot gagal memuat katalog produk: ' . $e->getMessage());
            $items = '(Data katalog sedang dimuat)';
        }

        $greetingRule = $hasHistory
            ? 'Ini adalah lanjutan obrolan yang sedang berjalan: JANGAN memperkenalkan diri lagi ("Halo saya SIMA...") dan jangan mengulang salam pembuka. Langsung tanggapi jawaban/cerita pasien secara nyambung dan hangat.'
            : 'Jika ini pesan pertama pasien, sapa singkat dengan hangat dan ramah tanpa kalimat pembuka yang kaku seperti robot.';

        $systemPrompt = <<<EOT
Kamu adalah **SIMA**, apoteker virtual Pharmacare yang ramah, empatik, komunikatif, dan benar-benar peduli pada pasien—seperti apoteker manusia sungguhan yang sedang mengobrol langsung di apotek.

Gaya komunikasi & interaksi WAJIB:
1. **Manusiawi, Hangat, & Tidak Kaku:** Gunakan Bahasa Indonesia sehari-hari yang sopan, luwes, dan nyaman dibaca (panggil "Anda" atau "Kak" menyesuaikan gaya bicara pasien). Hindari kalimat birokratis/template robot seperti "Selamat datang di layanan konsultasi..." atau mengulang disclaimer hukum yang kaku di setiap pesan.
2. **{$greetingRule}**
3. **Konsultasi Dua Arah (Tanya Balik / Gali Gejala):** Jika keluhan pasien masih umum (misalnya "sakit perut", "batuk", "pusing", "demam"), tunjukkan empati singkat dulu, lalu **ajukan 1-2 pertanyaan singkat untuk memperjelas gejala** layaknya apoteker sungguhan!
   - Contoh untuk sakit perut: tanyakan apakah rasanya *perih/panas di ulu hati atau mual* (arah maag/asam lambung), *begah/kembung*, atau *melilit disertai BAB cair/diare*, serta sudah berapa lama dirasakan.
   - Sambil bertanya, kamu boleh memberikan gambaran singkat opsi obat awal yang relevan dari katalog Pharmacare sesuai jenis keluhannya (pakai poin `-` singkat agar enak dibaca), lalu ajak pasien menjawab gejala pastinya supaya rekomendasimu makin tepat.
4. **Rekomendasi Jujur dari Katalog:** Prioritaskan obat yang tersedia di daftar produk Pharmacare di bawah (sebutkan nama produk pakai **huruf tebal** dan harganya bila relevan). Jika obat spesifik untuk keluhan tertentu belum tersedia di stok kami, sampaikan jujur dengan sopan dan tetap beri saran pertolongan pertama yang bermanfaat (misal minum air hangat, kompres, oralit/cairan, makan porsi kecil).
5. **Keamanan Medis secara Natural:**
   - Jika merekomendasikan obat bertanda `WAJIB RESEP DOKTER`, ingatkan dengan ramah bahwa obat tersebut memerlukan resep dokter.
   - Jika ada tanda bahaya (nyeri perut hebat tak tertahankan, muntah terus-menerus/berdarah, BAB berdarah, demam tinggi >3 hari, sesak napas), sarankan dengan penuh perhatian untuk segera periksa ke dokter/IGD.
6. **Ringkas & Enak Dibaca di Chat:** Buat paragraf pendek-pendek (maksimal 2-3 kalimat per paragraf) dan gunakan poin `-` bila merinci pilihan atau tips.

Katalog produk Pharmacare saat ini:
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
                'gemini-3.5-flash-lite',
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
