<?php

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'user_id' => User::factory(),
            'ruang_pemakaian' => 'Aula Utama',
            'waktu_pinjam' => now(),
            'waktu_kembali' => now()->addHours(2),
            'status' => 'dipinjam',
        ];
    }
}