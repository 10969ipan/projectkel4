<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CONTROLLER SATUAN
 * 
 * Menangani operasi CRUD (Create, Read, Update, Delete) untuk satuan barang
 * Satuan digunakan untuk mengukur barang (contoh: pcs, kg, meter, dll)
 */
class UnitController extends Controller
{
    /**
     * FUNGSI INDEX: Menampilkan daftar semua satuan
     * 
     * @return View
     */
    public function index(): View
    {
        // Ambil semua satuan dari database
        $units = Unit::all();

        return view('units.index', compact('units'));
    }

    /**
     * FUNGSI CREATE: Menampilkan form untuk membuat satuan baru
     * 
     * @return View
     */
    public function create(): View
    {
        return view('units.create');
    }

    /**
     * FUNGSI STORE: Menyimpan satuan baru ke database
     * 
     * @param Request $request - Request yang berisi data satuan
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // VALIDASI INPUT
        // - name: Wajib diisi, maksimal 255 karakter, harus unik (contoh: "Pieces", "Kilogram")
        // - symbol: Wajib diisi, maksimal 10 karakter, harus unik (contoh: "pcs", "kg")
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'symbol' => 'required|string|max:10|unique:units',
        ]);

        // Simpan satuan baru ke database
        Unit::create($request->all());

        // Redirect ke halaman daftar satuan dengan pesan sukses
        return redirect()
            ->route('units.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    /**
     * FUNGSI EDIT: Menampilkan form untuk mengedit satuan
     * 
     * @param Unit $unit - Satuan yang akan diedit (auto-binding dari route)
     * @return View
     */
    public function edit(Unit $unit): View
    {
        return view('units.edit', compact('unit'));
    }

    /**
     * FUNGSI UPDATE: Memperbarui data satuan di database
     * 
     * @param Request $request - Request yang berisi data satuan yang diupdate
     * @param Unit $unit - Satuan yang akan diupdate
     * @return RedirectResponse
     */
    public function update(Request $request, Unit $unit): RedirectResponse
    {
        // VALIDASI INPUT
        // - name: Wajib diisi, maksimal 255 karakter, harus unik kecuali untuk satuan ini sendiri
        // - symbol: Wajib diisi, maksimal 10 karakter, harus unik kecuali untuk satuan ini sendiri
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'symbol' => 'required|string|max:10|unique:units,symbol,' . $unit->id,
        ]);

        // Update data satuan di database
        $unit->update($request->all());

        // Redirect ke halaman daftar satuan dengan pesan sukses
        return redirect()
            ->route('units.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    /**
     * FUNGSI DESTROY: Menghapus satuan dari database
     * 
     * PENTING: Satuan tidak bisa dihapus jika masih memiliki barang terkait
     * 
     * @param Unit $unit - Satuan yang akan dihapus
     * @return RedirectResponse
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        // VALIDASI: Cek apakah satuan masih memiliki barang terkait
        // Jika masih ada barang yang menggunakan satuan ini, tidak boleh dihapus
        // Contoh: Jika ada barang dengan satuan "pcs", maka satuan "pcs" tidak bisa dihapus
        if ($unit->items()->count() > 0) {
            return back()->with('error', 'Satuan tidak bisa dihapus karena masih memiliki barang terkait.');
        }

        // Hapus satuan dari database
        $unit->delete();

        // Redirect ke halaman daftar satuan dengan pesan sukses
        return redirect()
            ->route('units.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
}