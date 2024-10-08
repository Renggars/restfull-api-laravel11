<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'tes')->first();
        Contact::create([
            'first_name' => 'tes',
            'last_name' => 'tes',
            'email' => 'tes@gmail.com',
            'phone' => 'tes',
            'user_id' => $user->id
        ]);
    }
}
