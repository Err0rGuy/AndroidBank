<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('tests account creation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $response = $this->post(route('account.create'));
    $response->assertCreated();

    // repeating response --> every user can only have one account
    $response = $this->post(route('account.create'));
    $response->assertBadRequest();
});
