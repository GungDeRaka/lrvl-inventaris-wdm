<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Transaksi;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Mendapatkan semua transaksi yang dilakukan oleh siswa.
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}