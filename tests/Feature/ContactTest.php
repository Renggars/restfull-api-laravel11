<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class]);

        $response = $this->post(
            '/api/contacts',
            [
                'first_name' => 'tes',
                'last_name' => 'tes',
                'email' => 'tes@gmail.com',
                'phone' => 'tes'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(201)->assertJson([
            'data' => [
                'first_name' => 'tes',
                'last_name' => 'tes',
                'email' => 'tes@gmail.com',
                'phone' => 'tes'
            ]
        ]);
    }

    public function testCreateEmailFailed(): void
    {
        $this->seed([UserSeeder::class]);

        $response = $this->post(
            '/api/contacts',
            [
                'first_name' => 'tes',
                'last_name' => 'tes',
                'email' => 'bukan email',
                'phone' => 'tes'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'email' => [
                    'The email field must be a valid email address.'
                ]
            ]
        ]);
    }
    public function testCreateFirstNameFailed(): void
    {
        $this->seed([UserSeeder::class]);

        $response = $this->post(
            '/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'tes',
                'email' => 'tes@gmail.com',
                'phone' => 'tes'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ],
            ]
        ]);
    }

    public function testCreateUnauthorized(): void
    {
        $response = $this->post(
            '/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'tes',
                'email' => 'bukan email',
                'phone' => 'tes'
            ]
        );

        $response->assertStatus(401)->assertJson([
            'errors' => [
                'message' => 'Unautorized'
            ]
        ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->get(
            '/api/contacts/' . $contact->id,
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'tes',
                'last_name' => 'tes',
                'email' => 'tes@gmail.com',
                'phone' => 'tes',
            ]
        ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->get(
            '/api/contacts/' . ($contact->id + 1),
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

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->get(
            '/api/contacts/' . $contact->id,
            [
                'Authorization' => 'Bearer ' . 'tes2'
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->put(
            '/api/contacts/' .  $contact->id,
            [
                'first_name' => 'update',
                'last_name' => 'update',
                'email' => 'update@gmail.com',
                'phone' => 'update'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'update',
                'last_name' => 'update',
                'email' => 'update@gmail.com',
                'phone' => 'update'
            ]
        ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->put(
            '/api/contacts/' .  $contact->id,
            [
                'first_name' => '',
                'last_name' => 'update',
                'email' => 'update@gmail.com',
                'phone' => 'update'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->delete(
            '/api/contacts/' .  $contact->id,
            [],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(200)->assertJson([
            'message' => 'Contact deleted'
        ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->delete(
            '/api/contacts/' .  ($contact->id + 1),
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
}
