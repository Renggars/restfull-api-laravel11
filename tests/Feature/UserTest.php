<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function testRegisterSuccess()
    {
        $response = $this->post('/api/users', [
            'username' => 'john',
            'password' => 'rahasia',
            'name' => 'putra'
        ]);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'username' => 'john',
                'name' => 'putra'
            ]
        ]);
    }

    public function testRegisterFailed()
    {
        $response = $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ]);

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'username' => [
                    'The username field is required.',
                ],
                'password' => [
                    'The password field is required.'
                ],
                'name' => [
                    'The name field is required.'
                ],
            ]
        ]);
    }

    public function testRegisterUsernameAlreadyExits()
    {
        $this->testRegisterSuccess();
        $response = $this->post('/api/users', [
            'username' => 'john',
            'password' => 'rahasia',
            'name' => 'putra'
        ]);

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'username' => [
                    'username already registered',
                ],
            ]
        ]);
    }
}
