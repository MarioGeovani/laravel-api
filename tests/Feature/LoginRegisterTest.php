<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginRegisterTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_the_route_register_a_new_user_with_success(): void
    {
        $name = $this->faker()->name();
        $email = $this->faker()->email();
        $password = $this->faker()->password(9,12);

        $response = $this->post(route('user.register', [
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
        ]));

        $response->assertJsonIsObject()->assertCreated();
        $this->assertDatabaseHas('users', [
            'name'      => $name,
            'email'     => $email
        ]);
    }

    public function test_the_route_register_a_new_user_with_error(): void
    {
        $name = $this->faker()->name();
        $email = $this->faker()->email() . '?';
        $password = $this->faker()->password(5,5);

        $response = $this->post(route('user.register', [
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
        ]));

        $response->assertSessionHasErrors();
        $errors = session('errors');
        $this->assertEquals($errors->default->messages(), [
                                "email" => ["The email field must be a valid email address."],
                                "password" => ["The password field must be at least 8 characters."]
                            ]);

        $this->assertDatabaseMissing('users', [
            'name'      => $name,
            'email'     => $email
        ]);
    }

    public function test_the_route_register_a_new_user_and_login_with_error(): void
    {
        $name = $this->faker()->name();
        $email = $this->faker()->email();
        $password = $this->faker()->password(9,12);

        $this->post(route('user.register', [
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
        ]));

        $response = $this->post(route('user.login', [
            'email'     => $email,
            'password'  => 'wrong_password',
        ]));

        $response->assertJsonIsObject()->assertUnauthorized();

        $this->assertEquals($response->original, [
            "status" => "failed",
            "message" => "Invalid credentials"
          ]);

    }

    public function test_the_route_register_a_new_user_and_login_with_success(): void
    {
        $name = $this->faker()->name();
        $email = $this->faker()->email();
        $password = $this->faker()->password(9,12);

        $this->post(route('user.register', [
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
        ]));

        $response = $this->post(route('user.login', [
            'email'     => $email,
            'password'  => $password,
        ]));

        $response->assertJsonIsObject()->IsOk();
        $this->assertEquals('success', $response->original['status']);
        $this->assertEquals('User is logged in successfully.', $response->original['message']);
    }

    public function test_the_route_register_a_new_user_and_login_with_success_and_get_details_by_token(): void
    {
        $name = $this->faker()->name();
        $email = $this->faker()->email();
        $password = $this->faker()->password(9,12);

        $this->post(route('user.register', [
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
        ]));

        $responseUserLogin = $this->post(route('user.login', [
            'email'     => $email,
            'password'  => $password,
        ]));

        $token = $responseUserLogin->original['data']['token'];

        //create dummy access to by pass midleware
        $user = User::factory()->create(); // Create a user
        $this->actingAs($user);

        $responseUserDetails = $this->get(route('user.details', []), [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertEquals(json_decode($user), json_decode($responseUserDetails->original));
        $responseUserDetails->assertJsonIsObject()->IsOk();
    }

}
