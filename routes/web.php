<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Siswa\LoginController;
use App\Livewire\Barang\Index as BarangIndex;
use App\Livewire\User\Index as UserIndex;
use App\Livewire\Kategori\Index as KategoriIndex;
use App\Livewire\Ruangan\Index as RuanganIndex;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'cek.jam.kerja'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard\Index::class)->name('dashboard');

    // Rute untuk Manajemen Barang
    Route::get('/barang', BarangIndex::class)->name('barang.index');

    Route::get('/laporan/transaksi', [LaporanController::class, 'cetakTransaksi'])->name('laporan.transaksi');
    // Rute untuk manajemen pengguna
    Route::get('/pengguna', UserIndex::class)->name('user.index')->middleware('can:kelola-pengguna');
    // Rute untuk manajemen kategori
    Route::get('/kategori', KategoriIndex::class)->name('kategori.index')->middleware('can:kelola-pengguna');
    // Rute untuk manajemen ruangan
    Route::get('/ruangan', RuanganIndex::class)->name('ruangan.index')->middleware('can:kelola-pengguna');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('siswa')->name('siswa.')->group(function () {
    // Rute untuk login & logout
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Halaman yang terproteksi (hanya bisa diakses setelah login sebagai siswa)
    Route::middleware('auth:siswa')->group(function () {
        Route::get('/dashboard', \App\Livewire\Siswa\Dashboard::class)->name('dashboard');
    });
});


require __DIR__ . '/auth.php';
