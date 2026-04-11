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
     * AI Auto-Reply menggunakan Google Gemini API
     */
    public function aiReply(Request $request)
    {
        // Log awal sekali untuk memastikan request sampai
        \Log::info('AI Bot hit: ' . $request->message);

        $request->validate([
            'message' => 'required|string|max:1000',
            'doctor_id' => 'nullable|exists:users,id'
        ]);

        $apiKey = config('services.groq.api_key');
        if (!$apiKey) {
            return response()->json(['reply' => 'Maaf, layanan AI sedang tidak tersedia.']);
        }

        // Ambil daftar obat dari database untuk konteks AI
        // Ditambah category_id agar relasi with('category') tidak null
        $items = Item::with('category')
            ->select('id', 'name', 'description', 'requires_prescription', 'category_id')
            ->limit(50)
            ->get()
            ->map(fn($i) => "- {$i->name}" . ($i->requires_prescription ? ' [RESEP WAJIB]' : '') . ($i->description ? ": {$i->description}" : ''))
            ->join("\n");

        $systemPrompt = <<<EOT
Kamu adalah **Apoteker Profesional Pharmacare**.
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

        try {
            // Menggunakan Groq API (OpenAI Compatible)
            $response = Http::withToken($apiKey)->withoutVerifying()->timeout(30)->post(
                'https://api.groq.com/openai/v1/chat/completions',
                [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $request->message]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                    'top_p' => 1,
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? '';

                if (empty($reply)) {
                    $reply = 'Maaf, saya sedang kesulitan memproses jawaban saat ini.';
                }
            } else {
                \Log::error('Chatbot API Error (' . $response->status() . '): ' . $response->body());
                $reply = 'Maaf, layanan sedang sibuk. Silakan coba kembali dalam beberapa saat.';
            }
        } catch (\Exception $e) {
            \Log::error('Chatbot Exception: ' . $e->getMessage());
            $reply = 'Gagal terhubung dengan pusat AI. Silakan periksa koneksi internet Anda atau coba lagi nanti.';
        }

        // SIMPAN HISTORY (Hanya jika ada doctor_id dan user login)
        // Karena kolom doctor_id di DB bersifat wajib (NOT NULL), kita lewati simpan jika NULL
        if (Auth::check() && $request->filled('doctor_id')) {
            TelemedicineChat::create([
                'user_id'       => Auth::id(),
                'doctor_id'     => $request->doctor_id,
                'message'       => $request->message,
                'is_from_doctor'=> false,
            ]);
            TelemedicineChat::create([
                'user_id'       => Auth::id(),
                'doctor_id'     => $request->doctor_id,
                'message'       => $reply,
                'is_from_doctor'=> true,
            ]);
        }

        return response()->json(['reply' => $reply]);
    }
}
