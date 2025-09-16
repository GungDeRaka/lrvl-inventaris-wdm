<?php

use App\Models\Barang;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Mendapatkan data siswa yang melakukan transaksi.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Mendapatkan data barang yang ditransaksikan.
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Mendapatkan data user (admin/penjaga) yang mengelola transaksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
