<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TransactionHelper
{
    public static function transferTransaction(Request $request): array
    {
        $user = $request->user();
        $fromAccount = $user->account;
        $amount = $request->input('amount');
        $toAccount = Account::query()
            ->where('account_number', $request->input('account_number'))->first();
        if (!$toAccount)
            return ['message' => "Account doesn't exists", 'status' => false];

        if (!Hash::check($request->input('password'), $user->password))
            return ['message' => 'Invalid credentials', 'status' => false];

        if ($toAccount->account_number === $fromAccount->account_number)
            return ['message' => 'From account and to account are the same', 'status' => false];

        if (($fromAccount->balance - $amount) < 5)
            return ['message' => 'Not enough balance for this transfer', 'status' => false];

        DB::transaction(function () use ($fromAccount, $toAccount, $amount, $request, $user){
            $fromAccount->decrement('balance', $amount);
            $toAccount->increment('balance', $amount);
            Transaction::query()->create([
                'user_id' => $user->id,
               'destination_account' => $toAccount->account_number,
               'amount' => $amount,
               'status' => 'success',
               'type' => 'transfer',
                'description' => $request->input('description'),
                'reference_number' => self::generateReferenceNumber()
            ]);

            $fromAccount->last_transaction_at = now();
            $toAccount->last_transaction_at = now();
            $fromAccount->save();
            $toAccount->save();
        });
        return ['message' => 'Transfer was successful', 'status' => true];
    }
    public static function generateReferenceNumber(): string
    {
        do {
            $number = mt_rand(1_000_000_000_000, 9_000_000_000_000);
        }while(Transaction::query()->where('reference_number', $number)->exists());
        return (string) $number;
    }

}
