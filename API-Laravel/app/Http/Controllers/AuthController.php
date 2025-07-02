<?php

namespace App\Http\Controllers;

use App\Helpers\AccountHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = User::query()->create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password'])
        ]);

        Account::query()->create([
            'user_id' => $user->id,
            'balance' => 100_000,
            'account_number' => AccountHelper::generateAccountNumber()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration was successful!',
            'user' => $user
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        if (!User::query()->where('email', $credentials['email'])->exists())
            return response()->json([
                'status' =>'fail',
                'message' => 'Email does not exist'
            ], 401);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = User::query()->find($request->user()->id);
            $account = $user->account;
            return response()->json([
                'status' => 'success',
                'message' => 'Login was successful',
                'user' => $user,
                'account' => $account
            ]);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'Wrong password'
        ], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        if (!$request->user())
            return response()->json(['message' => 'User not logged in'], 400);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    }

    public function detail(Request $request): JsonResponse
    {
        if (auth()->check()) {
            $account = Account::query()->where('user_id', $request->user()->id)->first();
            return response()->json(['user' => $request->user(), 'account' => $account]);
        }
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    public function checkLogin(): JsonResponse
    {
        return response()->json(['status' => auth()->check()]);
    }
}
