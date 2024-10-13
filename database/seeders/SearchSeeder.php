<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'tes')->first();
        if (!$user) {
            Log::error('User with username "tes" not found.');
        } else {
            Log::info('Seeding contacts for user: ' . $user->id);
            for ($i = 0; $i < 20; $i++) {
                Contact::create([
                    'first_name' => 'firstname' . $i,
                    'last_name' => 'lastname' . $i,
                    'email' => 'tes' . $i . '@gmail.com',
                    'phone' => '12345' . $i,
                    'user_id' => $user->id
                ]);
            };
            Log::info('20 contacts seeded successfully.');
        }
    }
}
