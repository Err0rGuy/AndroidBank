<?php

namespace App\Http\Controllers;

use App\Helpers\AccountHelper;
use App\Helpers\TransactionHelper;
use App\Http\Requests\TransferRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        if (!auth()->check())
            return response()->json(['message' => 'user is not authorized'], 401);
        $user = $request->user();
        if (Account::query()->where('user_id', $user->id)->exists())
            return response()->json(['message' => 'user already have an account'], 400);
        $account = $user->account()
            ->create([
               'account_number' => AccountHelper::generateAccountNumber(),
                'balance' => 1_000_000_000
            ]);
        return response()->json(['message' => 'Account created successfully', 'account' => $account], 201);
    }

}
