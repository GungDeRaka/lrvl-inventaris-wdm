<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Ganti Model menjadi Authenticatable
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Siswa extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    protected function formattedNoHp(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Hapus spasi, strip, dll.
                $no_hp = preg_replace('/[^0-9]/', '', $this->no_hp);

                // Ganti 0 di depan dengan 62
                if (substr($no_hp, 0, 1) === '0') {
                    return '62' . substr($no_hp, 1);
                }

                // Jika sudah pakai 62, biarkan
                if (substr($no_hp, 0, 2) === '62') {
                    return $no_hp;
                }

                // Default, tambahkan 62 di depan
                return '62' . $no_hp;
            },
        );
    }
}
