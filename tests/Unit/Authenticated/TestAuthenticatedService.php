<?php

namespace Tests\Unit\Authenticated;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestAuthenticatedService extends TestCase
{

    use RefreshDatabase;

    const URL = "http://localhost:8070/api/auth";

    /**
     * Test Login with valid credentials
     * @return void
     */
    public function test_login_with_valid_credentials(): void
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Make a POST request to the login route with valid credentials
        $response = $this->json('POST', self::URL . '/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Assert that the response contains the token
        $response->assertJsonStructure(['token']);

        // Assert that the user is authenticated
        $this->assertTrue(Auth::check());

        // Assert that the authenticated user is the same as the created user
        $this->assertEquals($user->id, Auth::user()->id);

    }

    /**
     * Test Login with invalid credentials
     * @return void
     */
    public function test_login_with_invalid_credentials(): void
    {
        // Make a POST request to the login route with invalid credentials
        $response = $this->json('POST', self::URL . '/login', [
            'email' => 'test@example.com',
            'password' => 'incorrect_password',
        ]);

        // Assert that the response has a 401 status code
        $response->assertStatus(401);

        // Assert that the response contains the error message
        $response->assertJson(['error' => 'Invalid credentials']);

        // Assert that the user is not authenticated
        $this->assertFalse(Auth::check());
    }

    /**
     * Test Register Function
     * @return void
     */
    public function test_register()
    {
        // Create a fake user using a model factory
        $user = User::factory()->make();

        // Generate fake credentials using the created user
        $credentials = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ];

        // Send a POST request to the register endpoint with the fake credentials
        $response = $this->json('POST', self::URL . '/register', $credentials);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Retrieve the user from the database
        $user = User::where('email', $credentials['email'])->first();

        // Assert that the response contains the generated token
        $response->assertJson([
            'token' => true,
            'user' => [
                "id" => $user->id,
                'name' => $credentials['name'],
                'email' => $credentials['email'],
                "created_at" => Carbon::parse($user->created_at)->toISOString(),
                "updated_at" => Carbon::parse($user->updated_at)->toISOString()
            ],
        ]);
    }

    /**
     * Test Logout function
     * @return void
     */
    public function test_logout(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Set the Authorization header with the JWT token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Make a POST request to logout
        $response = $this->post(self::URL.'/logout', [], $headers);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

    }

    /**
     * Test Refresh Token
     * @return void
     */
    public function test_refresh_token(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Set the Authorization header with the JWT token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Make a POST request to refresh the token
        $response = $this->post(self::URL.'/refresh', [], $headers);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Assert that a new token is returned in the response
        $response->assertJsonStructure(['token']);
    }
}
