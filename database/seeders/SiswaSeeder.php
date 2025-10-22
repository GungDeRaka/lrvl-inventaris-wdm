<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash; 

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // agr tidak membuat tabel baru ketika ada pihak luar yang ingin meminjam barang, pihak luar akan diinputkan sebagai siswa dengan nis 0000
        Siswa::create([
            'nis' => '0000',
            'nama' => 'Pihak Luar SMK',
            'email' => 'xx@siswa.widiatmika.sch.id',
            'kelas' => 'LUAR SMK',
            'no_hp' => '081234567890',
            'password' => Hash::make('password'),
        ]);

        Siswa::create([
            'nis' => '4774',
            'nama' => 'Budi Santoso',
            'email' => 'budi@siswa.widiatmika.sch.id',
            'kelas' => 'XII RPL 1',
            'no_hp' => '081234567890',
            'password' => Hash::make('password'),
        ]);

        Siswa::create([
            'nis' => '4775',
            'nama' => 'Citra Lestari',
            'email' => 'citra@siswa.widiatmika.sch.id',
            'kelas' => 'XI DKV 2',
            'no_hp' => '081209876543',
            'password' => Hash::make('password'),
        ]);
    }
}
