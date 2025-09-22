<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $kategoriElektronik = Kategori::where('nama_kategori', 'Elektronik')->first()->id;
        $ruanganLab = Ruangan::where('nama_ruangan', 'Lab Multimedia')->first()->id;

        Barang::create([
            'kode_barang' => 'PRO-001',
            'nama_barang' => 'Laptop Asus ROG',
            'kategori_id' => $kategoriElektronik,
            'ruangan_id' => $ruanganLab,
            'jumlah_total' => 5,
            'jumlah_saat_ini' => 5,
        ]);
        
        Barang::create([
            'kode_barang' => 'KBL-001',
            'nama_barang' => 'Kabel HDMI 5m',
            'kategori_id' => $kategoriElektronik,
            'ruangan_id' => $ruanganLab,
            'jumlah_total' => 10,
            'jumlah_saat_ini' => 10,
        ]);
    }
}
