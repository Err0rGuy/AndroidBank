<?php

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('proceed balance check transaction', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create();

    // requested by unauthenticated user
    $response = $this->post(route('transaction.check-balance'));
    $response->assertUnAuthorized();

    // requested by authenticated user
    $this->actingAs($user);
    $response = $this->post(route('transaction.check-balance'));
    $response->assertOk();
    expect($response['balance'])->toBe($account->balance);
});

it('proceed transfer transaction', function () {
    $user1 = User::factory()->create(
        [
            'password' => bcrypt('passWord')
        ]
    );
    $user2 = User::factory()->create();

    Account::factory()->create(['user_id' => $user1->id]);
    Account::factory()->create(['user_id' => $user2->id]);

    $senderFirstBalance = $user1->account->balance;
    $receiverFirstBalance = $user2->account->balance;

    $amount = $user1->account->balance % 100;

    $this->actingAs($user1);

    $response = $this->post(route('transaction.transfer'),
        [
            'password' => 'passWord',
            'account_number' => $user2->account->account_number,
            'amount' => $amount,
        ]
    );
    $response->assertOk();

    expect($senderFirstBalance - $amount)->toBe($user1->account->balance)
       ->and($receiverFirstBalance)->toBe($user2->account->balance);

});
