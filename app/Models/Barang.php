<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Transaksi;

class Barang extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Mendapatkan kategori dari barang ini.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Mendapatkan ruangan dari barang ini.
     */
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    /**
     * Mendapatkan semua riwayat transaksi untuk barang ini.
     */
    public function transaksis()
    {
        return $this->belongsToMany(Transaksi::class, 'barang_transaksi')
            ->withPivot('kuantitas')
            ->withTimestamps();
    }
}
