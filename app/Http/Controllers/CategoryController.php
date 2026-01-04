<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CONTROLLER KATEGORI
 * 
 * Menangani operasi CRUD (Create, Read, Update, Delete) untuk kategori barang
 * Kategori digunakan untuk mengelompokkan barang (contoh: Pakaian, Elektronik, dll)
 */
class CategoryController extends Controller
{
    /**
     * FUNGSI INDEX: Menampilkan daftar semua kategori
     * 
     * @return View
     */
    public function index(): View
    {
        // Ambil semua kategori dari database
        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    /**
     * FUNGSI CREATE: Menampilkan form untuk membuat kategori baru
     * 
     * @return View
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * FUNGSI STORE: Menyimpan kategori baru ke database
     * 
     * @param Request $request - Request yang berisi data kategori
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // VALIDASI INPUT
        // - name: Wajib diisi, maksimal 255 karakter, harus unik (tidak boleh duplikat)
        // - description: Opsional, berupa teks
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        // Simpan kategori baru ke database
        Category::create($request->all());

        // Redirect ke halaman daftar kategori dengan pesan sukses
        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * FUNGSI EDIT: Menampilkan form untuk mengedit kategori
     * 
     * @param Category $category - Kategori yang akan diedit (auto-binding dari route)
     * @return View
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * FUNGSI UPDATE: Memperbarui data kategori di database
     * 
     * @param Request $request - Request yang berisi data kategori yang diupdate
     * @param Category $category - Kategori yang akan diupdate
     * @return RedirectResponse
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        // VALIDASI INPUT
        // - name: Wajib diisi, maksimal 255 karakter, harus unik kecuali untuk kategori ini sendiri
        //   (menggunakan 'unique:categories,name,' . $category->id untuk mengabaikan ID kategori saat ini)
        // - description: Opsional, berupa teks
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        // Update data kategori di database
        $category->update($request->all());

        // Redirect ke halaman daftar kategori dengan pesan sukses
        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * FUNGSI DESTROY: Menghapus kategori dari database
     * 
     * PENTING: Kategori tidak bisa dihapus jika masih memiliki barang terkait
     * 
     * @param Category $category - Kategori yang akan dihapus
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        // VALIDASI: Cek apakah kategori masih memiliki barang terkait
        // Jika masih ada barang yang menggunakan kategori ini, tidak boleh dihapus
        if ($category->items()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki barang terkait.');
        }

        // Hapus kategori dari database
        $category->delete();

        // Redirect ke halaman daftar kategori dengan pesan sukses
        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}