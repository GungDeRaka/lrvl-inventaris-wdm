<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemindahanBarang extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function barangAsal(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_asal_id');
    }
    public function barangTujuan(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_tujuan_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
