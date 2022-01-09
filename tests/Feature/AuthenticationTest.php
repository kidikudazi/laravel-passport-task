<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    /**
     * Test register form validation
     *
     * @return void
     */
    public function testRegisterFormValidation()
    {
        $this->json('POST', 'api/v1/register', [], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors'
            ]);
    }

    /**
     * Test if password match in register
     *
     * @return void
     */
    public function testRegisterPasswordsComparison()
    {
        // Set form data
        $formData = [
            'password' => '12345678',
            'confirm_password' => '123456678'
        ];

        $this->json('POST', 'api/v1/register', $formData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'status' => 422,
                'errors' => [
                    'confirm_password'  => ['The confirm password and password must match.']
                ]
            ], 422);
    }

    /**
     * Test if registration is successful
     */
    public function testRegisterSuccessfully()
    {
        // Set form data
        $payload = [
            'fullname'          => 'John Doe',
            'email'             => 'johndoe@example.com',
            'phone'             => '12345678910',
            'password'          => '12345678',
            'confirm_password'  => '12345678'
        ];

        $this->json('POST', 'api/v1/register', $payload, ['Accept' => 'application/json'])
            ->assertStatus(201)
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

    /**
     * Test login form validation
     */
    public function testLoginFormValidation()
    {
        $this->json('POST', 'api/v1/login', [], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'status' => 422,
                'errors' => [
                    'email'     => ["The email field is required."],
                    'password'  => ["The password field is required."],
                ]
            ], 422);
    }

    /**
     * Test if login is successful
     */
    public function testLoginSuccessfully()
    {
        User::create([
            'name'      => 'John Doe',
            'email'     => 'johndoe@example.com',
            'phone'     => '12345678910',
            'password'  => Hash::make('12345678'),
        ]);

        // Set form data
        $payload = [
            'email'     => 'johndoe@example.com',
            'password'  => '12345678',
        ];

        $this->json('POST', 'api/v1/login', $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }
}
