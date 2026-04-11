<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TelemedicineChat;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ConsultationController extends Controller
{
    /**
     * Tampilkan daftar dokter (Direktori)
     */
    public function index()
    {
        // Cari user yang rolenya doctor (atau fiktif)
        $doctors = User::where('store_role', 'doctor')->get();
        return view('telemedicine.index', compact('doctors'));
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
            
        return view('telemedicine.chat', compact('doctor', 'chats'));
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
     * AI Auto-Reply menggunakan Google Gemini API
     */
    public function aiReply(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'doctor_id' => 'nullable|exists:users,id'
        ]);

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['reply' => 'Maaf, layanan AI sedang tidak tersedia. Silakan hubungi apoteker kami secara langsung.']);
        }

        // Ambil daftar obat dari database untuk konteks AI
        $items = Item::with('category')
            ->select('name', 'description', 'requires_prescription')
            ->limit(30)
            ->get()
            ->map(fn($i) => "- {$i->name}" . ($i->requires_prescription ? ' [RESEP WAJIB]' : '') . ($i->description ? ": {$i->description}" : ''))
            ->join("\n");

        $systemPrompt = <<<EOT
Kamu adalah **Apoteker Digital Pharmacare** yang berpengetahuan luas tentang farmasi.
Tugasmu adalah menjawab pertanyaan pelanggan tentang obat-obatan secara profesional, ramah, dan akurat dalam Bahasa Indonesia.

Panduan respons:
1. Berikan informasi: kegunaan, dosis umum, efek samping, cara penyimpanan bila relevan.
2. Jika obat memerlukan resep dokter, SELALU ingatkan pelanggan.
3. Untuk keluhan serius (nyeri dada, sesak nafas, dll), sarankan segera ke IGD.
4. Rekomendasikan produk dari daftar toko jika relevan.
5. Respons singkat, padat, dan menggunakan bahasa yang mudah dipahami (maks 3-4 paragraf).
6. JANGAN menggantikan diagnosis dokter, selalu sarankan konsultasi bila perlu.

Daftar produk yang tersedia di Pharmacare:
{$items}
EOT;

        try {
            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}",
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]]
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $request->message]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                        'topP' => 0.95,
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_NONE',
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_NONE',
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_NONE',
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_NONE',
                        ],
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                
                // Gabungkan semua part jika ada lebih dari satu
                $parts = $data['candidates'][0]['content']['parts'] ?? [];
                $reply = '';
                foreach ($parts as $part) {
                    $reply .= $part['text'] ?? '';
                }

                if (empty($reply)) {
                    $reply = 'Maaf, saya tidak dapat menjawab pertanyaan tersebut untuk alasan keamanan medis. Silakan konsultasikan langsung dengan dokter.';
                }
            } else {
                $reply = 'Maaf, layanan AI sementara tidak tersedia. Silakan hubungi apoteker kami secara langsung.';
            }
        } catch (\Exception $e) {
            $reply = 'Pesan gagal terkirim. Silakan periksa koneksi internet Anda atau coba lagi nanti.';
        }

        // Simpan percakapan jika user login (doctor_id bisa null untuk chat global)
        if (Auth::check()) {
            TelemedicineChat::create([
                'user_id'       => Auth::id(),
                'doctor_id'     => $request->doctor_id, // null jika chat global
                'message'       => $request->message,
                'is_from_doctor'=> false,
            ]);
            TelemedicineChat::create([
                'user_id'       => Auth::id(),
                'doctor_id'     => $request->doctor_id, // null jika chat global
                'message'       => $reply,
                'is_from_doctor'=> true,
            ]);
        }

        return response()->json(['reply' => $reply]);
    }
}
