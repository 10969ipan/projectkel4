<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * CONTROLLER LAPORAN (REPORT)
 * 
 * Menangani pembuatan dan download laporan dalam format PDF
 * Ada 3 jenis laporan:
 * 1. Laporan Stok Barang
 * 2. Laporan Transaksi (dengan filter tanggal)
 * 3. Laporan Permintaan Barang (dengan filter status dan tanggal)
 */
class ReportController extends Controller
{
    // ========================================================================
    // LAPORAN STOK BARANG
    // ========================================================================

    /**
     * FUNGSI STOCK REPORT: Menampilkan halaman laporan stok barang
     * 
     * Menampilkan semua barang dengan informasi kategori dan satuan
     * 
     * @return View
     */
    public function stockReport(): View
    {
        // Ambil semua barang dengan relasi category dan unit
        // Eager loading untuk menghindari N+1 query problem
        $items = Item::with(['category', 'unit'])->get();

        return view('reports.stock', compact('items'));
    }

    /**
     * FUNGSI DOWNLOAD STOCK REPORT: Generate dan download laporan stok dalam PDF
     * 
     * @return Response - File PDF untuk di-download
     */
    public function downloadStockReport(): Response
    {
        // Ambil semua barang dengan relasi
        $items = Item::with(['category', 'unit'])->get();

        // Generate PDF dari view reports.pdf.stock
        // Library yang digunakan: barryvdh/laravel-dompdf
        $pdf = Pdf::loadView('reports.pdf.stock', compact('items'));

        // Download PDF dengan nama file 'stock-report.pdf'
        return $pdf->download('stock-report.pdf');
    }

    // ========================================================================
    // LAPORAN TRANSAKSI
    // ========================================================================

    /**
     * FUNGSI TRANSACTION REPORT: Menampilkan halaman laporan transaksi
     * 
     * Fitur:
     * - Filter berdasarkan tanggal mulai (date_from)
     * - Filter berdasarkan tanggal akhir (date_to)
     * - Pagination 10 per halaman
     * 
     * @param Request $request - Request yang mungkin berisi filter tanggal
     * @return View
     */
    public function transactionReport(Request $request): View
    {
        // Query transaksi dengan relasi item dan user
        $transactions = Transaction::with(['item', 'user'])
            // FILTER TANGGAL MULAI: Jika ada parameter date_from
            // Hanya tampilkan transaksi >= tanggal ini
            ->when($request->date_from, function ($query) use ($request) {
                $query->where('date', '>=', $request->date_from);
            })
            // FILTER TANGGAL AKHIR: Jika ada parameter date_to
            // Hanya tampilkan transaksi <= tanggal ini
            ->when($request->date_to, function ($query) use ($request) {
                $query->where('date', '<=', $request->date_to);
            })
            // Urutkan dari yang terbaru
            ->latest()
            // Pagination 10 per halaman
            ->paginate(10);

        return view('reports.transaction', compact('transactions'));
    }

    /**
     * FUNGSI DOWNLOAD TRANSACTION REPORT: Generate dan download laporan transaksi dalam PDF
     * 
     * Menggunakan filter yang sama dengan transactionReport()
     * tapi tanpa pagination (ambil semua data yang sesuai filter)
     * 
     * @param Request $request - Request yang mungkin berisi filter tanggal
     * @return Response - File PDF untuk di-download
     */
    public function downloadTransactionReport(Request $request): Response
    {
        // Query transaksi dengan filter yang sama
        $transactions = Transaction::with(['item', 'user'])
            ->when($request->date_from, function ($query) use ($request) {
                $query->where('date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->where('date', '<=', $request->date_to);
            })
            ->latest()
            // PERBEDAAN: Gunakan get() bukan paginate()
            // Karena PDF perlu semua data sekaligus
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf.transaction', compact('transactions'));

        // Download PDF dengan nama file 'transaction-report.pdf'
        return $pdf->download('transaction-report.pdf');
    }

    // ========================================================================
    // LAPORAN PERMINTAAN BARANG
    // ========================================================================

    /**
     * FUNGSI REQUEST REPORT: Menampilkan halaman laporan permintaan barang
     * 
     * Fitur:
     * - Filter berdasarkan status (pending/approved/rejected)
     * - Filter berdasarkan tanggal mulai (date_from)
     * - Filter berdasarkan tanggal akhir (date_to)
     * - Pagination 10 per halaman
     * 
     * @param Request $request - Request yang mungkin berisi filter
     * @return View
     */
    public function requestReport(Request $request): View
    {
        // Query permintaan barang dengan relasi item, user, dan processedBy
        $requests = ItemRequest::with(['item', 'user', 'processedBy'])
            // FILTER STATUS: Jika ada parameter status
            // Tampilkan hanya permintaan dengan status tertentu
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            // FILTER TANGGAL MULAI: Berdasarkan created_at
            ->when($request->date_from, function ($query) use ($request) {
                $query->where('created_at', '>=', $request->date_from);
            })
            // FILTER TANGGAL AKHIR: Berdasarkan created_at
            ->when($request->date_to, function ($query) use ($request) {
                $query->where('created_at', '<=', $request->date_to);
            })
            // Urutkan dari yang terbaru
            ->latest()
            // Pagination 10 per halaman
            ->paginate(10);

        return view('reports.request', compact('requests'));
    }

    /**
     * FUNGSI DOWNLOAD REQUEST REPORT: Generate dan download laporan permintaan dalam PDF
     * 
     * Menggunakan filter yang sama dengan requestReport()
     * tapi tanpa pagination
     * 
     * @param Request $request - Request yang mungkin berisi filter
     * @return Response - File PDF untuk di-download
     */
    public function downloadRequestReport(Request $request): Response
    {
        // Query permintaan dengan filter yang sama
        $requests = ItemRequest::with(['item', 'user', 'processedBy'])
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->date_from, function ($query) use ($request) {
                $query->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->where('created_at', '<=', $request->date_to);
            })
            ->latest()
            // Gunakan get() untuk ambil semua data
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf.request', compact('requests'));

        // Download PDF dengan nama file 'request-report.pdf'
        return $pdf->download('request-report.pdf');
    }
}