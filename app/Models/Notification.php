<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// jalankan "php artisan app:cek-peminjaman-notifikasi" pada terminal untuk cek notifikasi
class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = []; // <-- TAMBAHKAN BARIS INI
}