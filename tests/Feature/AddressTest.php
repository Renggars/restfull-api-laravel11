<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\AddressSeeder;
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->get(
            '/api/contacts/' . $address->contact->id . '/addresses' . '/' . $address->id,
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => $address->id,
                'street' => 'street',
                'city' => 'city',
                'province' => 'province',
                'country' => 'country',
                'postal_code' => 'postal'
            ]
        ]);
    }

    public function testGetContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->get(
            '/api/contacts/' . ($address->contact->id + 1) . '/addresses' . '/' . $address->id,
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

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->get(
            '/api/contacts/' . $address->contact->id . '/addresses' . '/' . ($address->id + 1),
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'Address not found'
                ]
            ]
        ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->put(
            '/api/contacts/' . $address->contact->id . '/addresses' . '/' . $address->id,
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => 'postal'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(200)->assertJson([
            'data' => [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => 'postal'
            ]
        ]);
    }

    public function testUpdateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->put(
            '/api/contacts/' . ($address->contact->id + 1) . '/addresses' . '/' . $address->id,
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

    public function testUpdateAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->put(
            '/api/contacts/' . $address->contact->id . '/addresses' . '/' . ($address->id + 1),
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
                    'Address not found'
                ]
            ]
        ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->put(
            '/api/contacts/' . $address->contact->id . '/addresses' . '/' . $address->id,
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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->delete(
            '/api/contacts/' . $address->contact->id . '/addresses' . '/' . $address->id,
            [],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(200)->assertJson([
            'message' => 'Address deleted'
        ]);
    }

    public function testDeleteContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->delete(
            '/api/contacts/' . ($address->contact->id + 1) . '/addresses' . '/' . $address->id,
            [],
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

    public function testDeleteAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $response = $this->delete(
            '/api/contacts/' . $address->contact->id . '/addresses' . '/' . ($address->id + 1),
            [],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'Address not found'
                ]
            ]
        ]);
    }
}
