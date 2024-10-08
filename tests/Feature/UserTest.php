<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function PHPUnit\Framework\assertNotNull;

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

    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);
        $response = $this->get('/api/users/current', [
            'Authorization' => 'Bearer ' . 'tes',
        ]);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'tes',
                'name' => 'tes',
            ]
        ]);
    }
    public function testGetUnauthorized()
    {
        $response = $this->get('/api/users/current');
        $response->assertStatus(401)->assertJson([
            'errors' => [
                'message' => 'Unautorized'
            ]
        ]);
    }
    public function testGetInvalidToken()
    {
        $response = $this->get('/api/users/current', [
            'Authorization' => 'Bearer ' . 'invalid token'
        ]);
        $response->assertStatus(401)->assertJson([
            'errors' => [
                'message' => 'Invalid token'
            ]
        ]);
    }


    public function testUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'tes')->first();

        // Simulasi login dan dapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'username' => 'tes',
            'password' => 'tes'
        ]);

        $token = $loginResponse->json('data.token'); // asumsikan token ada di 'data.token'
        // dd($token);

        $response = $this->patch(
            '/api/users/current',
            [
                'password' => 'update password'
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'tes',
                'name' => 'tes',
            ]
        ]);

        $newUser = User::where('username', 'tes')->first();
        $this->assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'tes')->first();

        // Simulasi login dan dapatkan token
        // $loginResponse = $this->post('/api/users/login', [
        //     'username' => 'tes',
        //     'password' => 'update password' // sesuaikan dengan password user yang ada di database
        // ]);

        // $token = $loginResponse->json('data.token'); // asumsikan token ada di 'data.token'

        $response = $this->patch(
            '/api/users/current',
            [
                'name' => 'update name baru'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'tes',
                'name' => 'update name baru',
            ]
        ]);

        $newUser = User::where('username', 'tes')->first();
        $this->assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);

        // Simulasi login dan dapatkan token
        // $loginResponse = $this->post('/api/users/login', [
        //     'username' => 'tes',
        //     'password' => 'tes' // sesuaikan dengan password user yang ada di database
        // ]);

        // $token = $loginResponse->json('data.token'); // asumsikan token ada di 'data.token'

        $response = $this->patch(
            '/api/users/current',
            [
                'name' => '    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quidem quas nam illum qui non praesentium neque modi explicabo, dolores autem officiis, omnis adipisci! Libero quo nisi aliquid aliquam est culpa, nam perspiciatis hic saepe possimus consequatur eveniet vitae adipisci nihil id eos optio! Accusamus nisi fuga iste eos ex tempora corporis eaque vero consequuntur similique, modi voluptates maiores corrupti, tenetur optio nemo quaerat labore facere quasi sint nihil. Non blanditiis sunt pariatur voluptatum in, a tempore quidem. Soluta assumenda perspiciatis non ratione fugit fuga quas amet eaque iusto suscipit? Quasi expedita voluptates eum dolorum distinctio dicta deleniti numquam aspernatur voluptate.
'
            ],
            [
                'Authorization' => 'Bearer ' . 'tes'
            ]
        );

        $response->assertStatus(400)->assertJson([
            'errors' => [
                'name' => [
                    'The name field must not be greater than 100 characters.'
                ]
            ]
        ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);

        $response = $this->delete('/api/users/logout', [], [
            'Authorization' => 'Bearer ' . 'tes'
        ]);

        $response->assertStatus(204);

        $user = User::where('username', 'tes')->first();
        $this->assertNull($user->token);
    }

    public function testLogoutFailedInvalidToken()
    {
        $this->seed(UserSeeder::class);

        $this->delete('/api/users/logout', [], [
            'Authorization' => 'Bearer ' . 'salah'
        ])->assertStatus(401)->assertJson([
            'errors' => [
                'message' => 'Invalid token'
            ]
        ]);
    }

    public function testLogoutFailedTokenUnauthorized()
    {
        $this->seed(UserSeeder::class);

        $this->delete('/api/users/logout')->assertStatus(401)->assertJson([
            'errors' => [
                'message' => 'Unautorized'
            ]
        ]);
    }
}
