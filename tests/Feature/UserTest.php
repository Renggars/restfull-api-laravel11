<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
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

    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $response = $this->post('/api/users/login', [
            'username' => 'tes',
            'password' => 'tes'
        ]);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'tes',
                'name' => 'tes',
            ]
        ]);

        $user = User::where('username', 'tes')->first();
        $this->assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        // karena tidak menggunakan seeder, jadi user tidak ada
        $response = $this->post('/api/users/login', [
            'username' => 'usernotfound',
            'password' => 'tes'
        ]);

        $response->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'invalid username or password'
                ]
            ]
        ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed(UserSeeder::class);
        $response = $this->post('/api/users/login', [
            'username' => 'tes',
            'password' => 'password wrong'
        ]);

        $response->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'invalid username or password'
                ]
            ]
        ]);
    }
}
