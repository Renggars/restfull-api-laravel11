<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'street',
                'city' => 'city',
                'province' => 'province',
                'country' => 'country',
                'postal_code' => 'postal'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(201)->assertJson([
            'data' => [
                'street' => 'street',
                'city' => 'city',
                'province' => 'province',
                'country' => 'country',
                'postal_code' => 'postal'
            ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'street',
                'city' => 'city',
                'province' => 'province',
                'country' => '',
                'postal_code' => 'postal'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'country' => [
                    'The country field is required.'
                ],
            ]
        ]);
    }

    public function testContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->post(
            '/api/contacts/' . ($contact->id + 1) . '/addresses',
            [
                'street' => 'street',
                'city' => 'city',
                'province' => 'province',
                'country' => 'country',
                'postal_code' => 'postal'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'Contact not found'
                ]
            ]
        ]);
    }
}
