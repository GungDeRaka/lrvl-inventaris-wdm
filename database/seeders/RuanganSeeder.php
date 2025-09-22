<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ruangan::create(['nama_ruangan' => 'Gudang Utama']);
        Ruangan::create(['nama_ruangan' => 'Lab Multimedia']);
        Ruangan::create(['nama_ruangan' => 'Ruang Guru']);
    }
}
