<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * Test profile details by authenticated user
     *
     * @return void
     */
    public function testAuthenticatedUserGetDetails()
    {
        $user = User::create([
            'name'      => 'John Doe',
            'email'     => 'johndoe@example.com',
            'phone'     => '12345678910',
            'password'  => Hash::make('12345678')
        ]);

        $this->actingAs($user, 'api');

        $this->json('GET', '/api/v1/users/profile', [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /***
     * Test if user is not authenticated
     */
    public function testUnauthenticatedUser()
    {
        $this->json('GET', '/api/v1/users/profile', [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'message'  => 'Unauthenticated.'
            ], 401);

        $this->assertGuest('api');
    }
}
