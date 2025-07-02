<?php

namespace Database\Factories;

use App\Helpers\TransactionHelper;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inrandomOrder()->first()->id,
            'destination_account' => Account::inRandomOrder()->first()->account_number,
            'amount' => function (array $attributes) {
                return Account::query()->where('account_number', $attributes['account_number'])->get()->balance % 200;
            },
            'type' => 'transfer',
            'status' => fake()->randomElement(['success', 'fail']),
            'reference_number' => TransactionHelper::generateReferenceNumber()
        ];
    }
}
