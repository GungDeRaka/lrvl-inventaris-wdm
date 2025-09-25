<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Transaksi;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Mendapatkan semua transaksi yang dilakukan oleh siswa.
     */
    protected function formattedNoHp(): Attribute
    {
        return Attribute::make(
            get: function () {
                $no_hp = preg_replace('/[^0-9]/', '', $this->no_hp);
                if (substr($no_hp, 0, 1) === '0') {
                    return '62' . substr($no_hp, 1);
                }
                if (substr($no_hp, 0, 2) === '62') {
                    return $no_hp;
                }
                return '62' . $no_hp;
            },
        );
    }
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}
