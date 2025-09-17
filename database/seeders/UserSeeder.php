<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import model User
use Illuminate\Support\Facades\Hash; // Import Hash

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User Kepala Gudang
        User::create([
            'name' => 'Admin Kepala Gudang',
            'email' => 'kepala@widiatmika.sch.id',
            'password' => Hash::make('passwordkpl'),
            'peran' => 'kepala_gudang',
            'email_verified_at' => now(),
        ]);

        // User Penjaga Gudang
        User::create([
            'name' => 'Admin Penjaga Gudang',
            'email' => 'penjaga@widiatmika.sch.id',
            'password' => Hash::make('passwordpjg'),
            'peran' => 'penjaga_gudang',
            'email_verified_at' => now(),
        ]);
    }
}