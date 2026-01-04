<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CONTROLLER DASHBOARD
 * 
 * Menangani tampilan dashboard utama aplikasi
 * Dashboard menampilkan ringkasan statistik penting untuk monitoring
 */
class DashboardController extends Controller
{
    /**
     * FUNGSI INDEX: Menampilkan halaman dashboard dengan statistik
     * 
     * Dashboard menampilkan 3 metrik utama:
     * 1. Total barang yang terdaftar di sistem
     * 2. Jumlah permintaan barang yang menunggu persetujuan
     * 3. Jumlah barang dengan stok rendah (< 10)
     * 
     * @return View
     */
    public function index(): View
    {
        // METRIK 1: Hitung total semua barang yang terdaftar di sistem
        // Digunakan untuk mengetahui berapa banyak jenis barang yang dikelola
        $totalItems = Item::count();

        // METRIK 2: Hitung jumlah permintaan barang yang statusnya 'pending'
        // Permintaan pending adalah permintaan yang belum disetujui atau ditolak oleh admin
        // Metrik ini penting untuk admin agar tahu ada berapa permintaan yang perlu ditindaklanjuti
        $pendingRequests = ItemRequest::where('status', 'pending')->count();

        // METRIK 3: Hitung jumlah barang dengan stok rendah (kurang dari 10)
        // Ini adalah early warning system untuk restock barang
        // Threshold 10 bisa disesuaikan sesuai kebutuhan bisnis
        $lowStockItems = Item::where('stock', '<', 10)->count();

        // Kirim semua metrik ke view dashboard
        return view('dashboard', compact('totalItems', 'pendingRequests', 'lowStockItems'));
    }
}