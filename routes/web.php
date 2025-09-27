<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController; //
use App\Livewire\Barang\Index as BarangIndex;
use App\Livewire\User\Index as UserIndex;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'cek.jam.kerja'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard\Index::class)->name('dashboard');

    // Rute untuk Manajemen Barang
    Route::get('/barang', BarangIndex::class)->name('barang.index');

    Route::get('/laporan/transaksi', [LaporanController::class, 'cetakTransaksi'])->name('laporan.transaksi');
    Route::get('/pengguna', UserIndex::class)->name('user.index')->middleware('can:kelola-pengguna');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__ . '/auth.php';
