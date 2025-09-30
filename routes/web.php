<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController; //
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



require __DIR__ . '/auth.php';
