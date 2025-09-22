<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Siswa::create([
            'nis' => '4774',
            'nama' => 'Budi Santoso',
            'kelas' => 'XII RPL 1',
            'no_hp' => '081234567890'
        ]);
        Siswa::create([
            'nis' => '4775',
            'nama' => 'Citra Lestari',
            'kelas' => 'XI DKV 2',
            'no_hp' => '081209876543'
        ]);
    }
}
