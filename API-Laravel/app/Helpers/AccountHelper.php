<?php

namespace App\Helpers;

use App\Models\Account;

class AccountHelper
{

    public static function generateAccountNumber(): string
    {
        do{
            $number = mt_rand(1_000_000_000_000, 9_000_000_000_000);
        }while(Account::query()->where('account_number', $number)->exists());
        return (string)$number;
    }
}
