<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\LaporanPengadaanController;
use App\Http\Controllers\Siswa\LoginController;
use App\Http\Controllers\Siswa\Auth\PasswordResetController;
use App\Models\Barang;
use App\Livewire\Barang\Index as BarangIndex;
use App\Livewire\User\Index as UserIndex;
use App\Livewire\Kategori\Index as KategoriIndex;
use App\Livewire\Ruangan\Index as RuanganIndex;
use App\Livewire\Siswa\Index as SiswaIndex;
use App\Livewire\Siswa\Profil as SiswaProfil;
use App\Livewire\Rab\Index as RabIndex;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ==========================================================
    // 1. RUTE YANG BISA DIAKSES SEMUA ROLE (TERMASUK BENDAHARA)
    // ==========================================================
    
    // Manajemen RAB (Bendahara perlu akses ini)
    Route::get('/rab', RabIndex::class)->name('rab.index');

    // Profil User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ==========================================================
    // 2. RUTE KHUSUS ORANG GUDANG (DILARANG UNTUK BENDAHARA)
    //    Menggunakan Middleware 'can:akses-gudang'
    // ==========================================================
    Route::middleware(['can:akses-gudang'])->group(function () {
        
        Route::get('/dashboard', \App\Livewire\Dashboard\Index::class)->name('dashboard');

        // Manajemen Barang
        Route::get('/barang', BarangIndex::class)->name('barang.index');
        
        // Laporan & Cetak
        Route::get('/laporan/transaksi', [LaporanController::class, 'cetakTransaksi'])->name('laporan.transaksi');
        Route::get('/transaksi/{id}/cetak', [LaporanController::class, 'cetakStruk'])->name('transaksi.cetak');
        Route::get('/laporan/pengadaan/cetak', [LaporanPengadaanController::class, 'cetak'])->name('laporan.pengadaan.cetak');

        // Fitur AI / Prediksi
        Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
        Route::get('/prediksi/check', [PrediksiController::class, 'getPrediction'])->name('prediksi.check');
        Route::get('/prediksi/item', [PrediksiController::class, 'predictItem'])->name('prediksi.item');
        Route::get('/prediksi/ranking', [PrediksiController::class, 'getRanking'])->name('prediksi.ranking');
        
        // Utilitas Fix Stock
        Route::get('/fix-stock', function () {
            // ... (Kode fix stock Anda biarkan disini) ...
            return "Fix stock selesai";
        });
    });


    // ==========================================================
    // 3. RUTE SUPER ADMIN / KEPALA GUDANG (MANAJEMEN MASTER)
    // ==========================================================
    Route::middleware(['can:kelola-pengguna'])->group(function() {
        Route::get('/pengguna', UserIndex::class)->name('user.index');
        Route::get('/kategori', KategoriIndex::class)->name('kategori.index');
        Route::get('/ruangan', RuanganIndex::class)->name('ruangan.index');
        Route::get('/siswa', SiswaIndex::class)->name('siswa.index');
    });

});

// --- Rute Siswa (Tidak Berubah) ---
Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');

    Route::middleware('auth:siswa')->group(function () {
        Route::get('/dashboard', \App\Livewire\Siswa\Dashboard::class)->name('siswa.dashboard');
        Route::get('/profil', SiswaProfil::class)->name('profil');
        Route::get('/transaksi/{id}/cetak', [LaporanController::class, 'cetakStruk'])->name('transaksi.cetak');
    });
});

require __DIR__ . '/auth.php';