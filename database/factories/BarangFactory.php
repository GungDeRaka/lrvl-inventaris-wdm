<?php

namespace Database\Factories;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_barang' => $this->faker->words(2, true),
            'kode_barang' => $this->faker->unique()->bothify('BRG-####'),
            'ruangan_id' => Ruangan::factory(), // Otomatis buat ruangan jika tidak diisi
            'jumlah_total' => 10,
            'jumlah_saat_ini' => 10,
            'jumlah_rusak' => 0,
        ];
    }
}