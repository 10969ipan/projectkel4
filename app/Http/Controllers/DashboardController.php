<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

        // METRIK 3: Produk Kadaluwarsa (EWS)
        // Menghitung batch obat yang akan kadaluwarsa dalam 30 hari ke depan
        $expiringSoonCount = \App\Models\ItemSize::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();

        // METRIK 4: Produk Sudah Kadaluwarsa
        $expiredCount = \App\Models\ItemSize::where('expiry_date', '<=', now())->count();

        // PENDAPATAN 30 HARI TERAKHIR
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        $totalIncome = StoreOrder::whereIn('order_status', ['paid', 'processing', 'completed'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('grand_total');

        // TOP 5 OBAT TERLARIS (30 Hari Terakhir)
        $topItems = StoreOrderItem::selectRaw('item_id, sum(quantity) as total_qty, sum(sub_total) as total_sales')
            ->whereHas('order', function($q) use ($thirtyDaysAgo) {
                $q->whereIn('order_status', ['paid', 'processing', 'completed'])
                  ->where('created_at', '>=', $thirtyDaysAgo);
            })
            ->with('item')
            ->groupBy('item_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // GRAFIK PENJUALAN HARIAN (30 Hari Terakhir)
        $dailySalesRaw = StoreOrder::selectRaw('DATE(created_at) as date, SUM(grand_total) as daily_total')
            ->whereIn('order_status', ['paid', 'processing', 'completed'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->pluck('daily_total', 'date');

        // Isi hari yang kosong dengan 0
        $dates = [];
        $salesData = [];
        $currentDate = $thirtyDaysAgo->copy();
        
        while ($currentDate <= now()) {
            $dateString = $currentDate->format('Y-m-d');
            $dates[] = $currentDate->format('d M');
            $salesData[] = $dailySalesRaw->get($dateString) ?? 0;
            $currentDate->addDay();
        }

        // 10 LOG TRANSAKSI TERAKHIR (Store Orders)
        $latestStoreTransactions = StoreOrder::with(['user', 'address'])
            ->latest()
            ->take(10)
            ->get();

        // Kirim semua metrik ke view dashboard
        return view('backend.dashboard', compact(
            'totalItems', 'pendingRequests', 'expiringSoonCount', 'expiredCount',
            'totalIncome', 'topItems', 'dates', 'salesData', 'latestStoreTransactions'
        ));
    }
}