<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->limit(1)->first();

        $contact->addresses()->create([
            'contact_id' => $contact->id,
            'street' => 'street',
            'city' => 'city',
            'province' => 'province',
            'country' => 'country',
            'postal_code' => 'postal'
        ]);
    }
}
