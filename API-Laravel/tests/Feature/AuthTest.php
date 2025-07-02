<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

//beforeEach(function (){
//    $this->seed();
//});

it('Registers a new user', function () {

    $this->assertFalse(User::query()->where('email', 'test@gmail.com')->exists());
    $response = $this->post(route('register'), [
        'first_name' => 'john',
        'last_name' => 'doe',
        'email' => 'test@gmail.com',
        'password' => 'ThisIsAStrongPassword12334#@!',
        'password_confirmation' => 'ThisIsAStrongPassword12334#@!'
    ]);

    $response->assertStatus(201);
    $this->assertTrue(User::query()->where('email', 'test@gmail.com')->exists());
});


it('Logs in the users', function () {
    User::factory()->create([
       'email' => 'test@gmail.com',
       'password' => 'my_password'
    ]);
    $response = $this->post(route('account.create'));

    // Unauthorized user cannot create an account
    $response->assertStatus(401);

    $response = $this->post(route('login'), [
        'email' => 'test@gmail.com',
        'password' => 'my_password'
    ]);

    // User logged in
    $response->assertOk();

    $response = $this->post(route('account.create'));

    // authorized user can create an account
    $response->assertStatus(201);
});

it('logs out the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('logout'));
    $response->assertOk()
    ->assertJson([
        'message' => 'Logged out'
    ]);


});
