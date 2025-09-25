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
        //  Siswa::create([
        //     'nis' => '4774',
        //     'nama' => 'Budi Santoso',
        //     'kelas' => 'XII RPL 1',
        //     'no_hp' => '081234567890'
        // ]);
        // Siswa::create([
        //     'nis' => '4775',
        //     'nama' => 'Citra Lestari',
        //     'kelas' => 'XI DKV 2',
        //     'no_hp' => '081209876543'
        // ]);

        Siswa::create([
        'nis' => '4776',
        'nama' => 'Andi Wijaya',
        'kelas' => 'X TKJ 1',
        'no_hp' => '081345678912'
    ]);

    Siswa::create([
        'nis' => '4777',
        'nama' => 'Gung Raka',
        'kelas' => 'XII MM 3',
        'no_hp' => '082341822787'
    ]);

    Siswa::create([
        'nis' => '4778',
        'nama' => 'Rina Sari',
        'kelas' => 'XI RPL 2',
        'no_hp' => '081456789123'
    ]);
    }
}
