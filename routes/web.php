<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanPengadaanController;
use App\Http\Controllers\Siswa\LoginController;
use App\Http\Controllers\Siswa\Auth\PasswordResetController;
use App\Livewire\Barang\Index as BarangIndex;
use App\Livewire\User\Index as UserIndex;
use App\Livewire\Kategori\Index as KategoriIndex;
use App\Livewire\Ruangan\Index as RuanganIndex;
use App\Livewire\Siswa\Index as SiswaIndex;
use App\Livewire\Siswa\Profil as SiswaProfil;
use App\Livewire\Rab\Create as RabCreate;
use App\Livewire\Rab\Index as RabIndex;

Route::get('/', function () {
    return view('welcome');
});
// 'cek.jam.kerja'
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard\Index::class)->name('dashboard');

    // Rute untuk Manajemen Barang
    Route::get('/barang', BarangIndex::class)->name('barang.index');
// rute laporan transaksi berdasarkan periode tertentu
    Route::get('/laporan/transaksi', [LaporanController::class, 'cetakTransaksi'])->name('laporan.transaksi');

// rute cetak struk transaksi
    Route::get('/transaksi/{id}/cetak', [LaporanController::class, 'cetakStruk'])->name('transaksi.cetak');

    Route::get('/laporan/pengadaan/cetak', [LaporanPengadaanController::class, 'cetak'])
    ->name('laporan.pengadaan.cetak');
    
    // Rute untuk manajemen pengguna
    Route::get('/pengguna', UserIndex::class)->name('user.index')->middleware('can:kelola-pengguna');
    // Rute untuk manajemen kategori
    Route::get('/kategori', KategoriIndex::class)->name('kategori.index')->middleware('can:kelola-pengguna');
    // Rute untuk manajemen ruangan
    Route::get('/ruangan', RuanganIndex::class)->name('ruangan.index')->middleware('can:kelola-pengguna');
    // Rute untuk manajemen siswa
    Route::get('/siswa', SiswaIndex::class)->name('siswa.index')->middleware('can:kelola-pengguna');
// rute pengajuan RAB
    // Route::get('/rab/create', RabCreate::class)->name('rab.create');

    // Rute baru untuk daftar & persetujuan RAB
    Route::get('/rab', RabIndex::class)->name('rab.index');
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

    Route::get('forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');


    // Halaman yang terproteksi (hanya bisa diakses setelah login sebagai siswa)
    Route::middleware('auth:siswa')->group(function () {
        Route::get('/dashboard', \App\Livewire\Siswa\Dashboard::class)->name('dashboard');
        Route::get('/profil', SiswaProfil::class)->name('profil');
    });
});


require __DIR__ . '/auth.php';


// todo tambahin view riwayat penambahan barang di tab detail barang
// done tambahin riwayat pengajuan rab 
// todo tambahin cetak laporan tiap transaksi pada siswa dan admin
// 