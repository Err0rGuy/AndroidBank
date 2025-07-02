<?php


use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {

    Route::post('/register', 'register')->name('register')
    ->withoutMiddleware(VerifyCsrfToken::class);

    Route::post('/login', 'login')->name('login')
    ->withoutMiddleware(VerifyCsrfToken::class);

    Route::post('/logout', 'logout')->name('logout')
    ->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/detail', 'detail')->name('userDetail')
    ->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/check', 'checkLogin')->name('check')
        ->withoutMiddleware(VerifyCsrfToken::class);

});

Route::controller(AccountController::class)->group(function () {

    Route::post('/account/create', 'create')->name('account.create')
    ->withoutMiddleware(VerifyCsrfToken::class);

});

Route::controller(TransactionController::class)->group(function () {

    Route::post('/transaction/transfer', 'transferMoney')->name('transaction.transfer')
    ->withoutMiddleware(VerifyCsrfToken::class);

    Route::post('/transaction/check-balance', 'showBalance')->name('transaction.check-balance')
    ->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/transaction', 'showTransactions')->name('transaction.transactions')
    ->withoutMiddleware(VerifyCsrfToken::class);

});


Route::get('/ping', function () {

    return response('', 200);

});



