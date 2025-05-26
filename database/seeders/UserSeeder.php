<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'JoJo',
            'last_name' => 'Developer',
            'name' => 'JoJo Developer',
            'email' => 'giovannicastaldodev@gmail.com',
            'role' => 'Sviluppatore',
            'password' => Hash::make('K1t4mmu0rt!'),
        ]);
    }
}
