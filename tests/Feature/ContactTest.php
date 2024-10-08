<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UserSeeder;
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
}
