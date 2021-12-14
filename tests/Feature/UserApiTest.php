<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_a_user_can_be_added()
    {
        $response = $this->post('/api/register', [
            'username' => 'john.doe',
            'password' => 'secret'
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'token' => true
        ]);

        $this->assertCount(1, User::all());
    }

    /** @test */
    public function test_a_username_should_be_unique()
    {
        $response = $this->post('/api/register', [
            'username' => 'john.doe',
            'password' => 'secret'
        ]);
        $this->assertCount(1, User::all());

        $response = $this->post('/api/register', [
            'username' => 'john.doe',
            'password' => 'secret'
        ]);

        $this->assertCount(1, User::all());

        $response->assertJson([
            'username' => ['The username has already been taken.']
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_a_user_can_login()
    {

        $this->post('/api/register', [
            'username' => 'john.doe',
            'password' => 'secret'
        ]);
        $this->assertCount(1, User::all());

        $response = $this->post('/api/login', [
            'username' => 'john.doe',
            'password' => 'secret'
        ]);

        $response->assertJson([
            'token' => true
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function test_invalid_user_cannot_login()
    {

        $this->post('/api/register', [
            'username' => 'john.doe',
            'password' => 'secret'
        ]);
        $this->assertCount(1, User::all());

        $response = $this->post('/api/login', [
            'username' => 'john.doe',
            'password' => 'wrongpassword'
        ]);

        $response->assertJson([
            'error' => 'invalid_credentials'
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function test_user_can_access_unprotected_route()
    {
        $response = $this->get('/api/open');

        $response->assertStatus(200);

        $response->assertJson([
            'data' => 'This data is open and can be accessed without the client being authenticated'
        ]);
    }

    /** @test */
    public function test_unauthorized_user_cannot_access_protected_endpoint()
    {
        $response = $this->get('/api/closed');

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'Authorization Token not found'
        ]);
        $response->assertStatus(200);
    }
}
