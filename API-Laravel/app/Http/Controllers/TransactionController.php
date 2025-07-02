<?php

namespace App\Http\Controllers;

use App\Helpers\TransactionHelper;
use App\Http\Requests\TransferRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function showBalance(Request $request): JsonResponse
    {
        if (!auth()->check())
            return response()->json(['message' => 'user is not authorized'], 401);
        $user = $request->user();
        $account = $user->account;
        if ($account)
            return response()->json(['balance' => $account->balance]);
        return response()->json(['message' => "account doesn't exists"]);
    }

    public function transferMoney(TransferRequest $request): JsonResponse
    {
        $result = TransactionHelper::transferTransaction($request);
        if ($result['status'])
            return response()->json(['message' => $result['message']]);
        return response()->json(['message' => $result['message']], 400);
    }

    public function showTransactions(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(['transactions' => $user->transactions]);
    }

}
