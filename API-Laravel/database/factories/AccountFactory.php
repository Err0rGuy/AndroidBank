<?php

namespace Database\Factories;

use App\Helpers\AccountHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'account_number' => AccountHelper::generateAccountNumber(),
            'balance' => rand(1000, 9_000_000_000)
        ];
    }
}
