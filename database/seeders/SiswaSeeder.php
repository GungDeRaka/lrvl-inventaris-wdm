<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash; // <-- Tambahkan ini

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        Siswa::create([
            'nis' => '4774',
            'nama' => 'Budi Santoso',
            'email' => 'budi@siswa.widiatmika.sch.id', // <-- Tambah email
            'kelas' => 'XII RPL 1',
            'no_hp' => '081234567890',
            'password' => Hash::make('password'), // <-- Tambah password
        ]);
        
        Siswa::create([
            'nis' => '4775',
            'nama' => 'Citra Lestari',
            'email' => 'citra@siswa.widiatmika.sch.id', // <-- Tambah email
            'kelas' => 'XI DKV 2',
            'no_hp' => '081209876543',
            'password' => Hash::make('password'), // <-- Tambah password
        ]);
    }
}