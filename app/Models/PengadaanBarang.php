<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class PengadaanBarang extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     * Menggunakan guarded kosong agar semua field bisa diisi.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Mendapatkan data barang yang terkait dengan pengadaan ini.
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
