<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'tes',
            'password' => Hash::make('tes'),
            'name' => 'tes',
            'token' => 'tes'
        ]);

        User::create([
            'username' => 'tes2',
            'password' => Hash::make('tes2'),
            'name' => 'tes2',
            'token' => 'tes2'
        ]);
    }
}
