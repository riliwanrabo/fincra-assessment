<?php

use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\FundsTransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PagesController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\ExperimentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\WebhookController;

Route::controller(PagesController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('health', 'health');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', LoginController::class);
    Route::post('register', RegistrationController::class);
    Route::post('logout', LogoutController::class);
});

Route::controller(ProfileController::class)
    ->prefix('profile')
    ->group(function () {
        Route::get('me', 'show');
        Route::get('transactions', 'transactions');
    });


Route::controller(UserController::class)
    ->prefix('users')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{user}', 'show');
        Route::put('/', 'update');
    });

Route::controller(WalletController::class)
    ->prefix('wallets')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('fund', 'fundWallet');
        Route::post('debit', 'debitWallet');

        // wallet transfers
        Route::prefix('transfers')->group(function () {
            Route::post('/', 'walletTransfer');
        });
    });

Route::controller(TransactionController::class)
    ->prefix('transactions')
    ->group(function () {
        Route::get('/', 'index');
    });

Route::controller(BankController::class)
    ->prefix('banks')
    ->group(function () {
        Route::get('/', 'index');
    });


Route::controller(FundsTransferController::class)
    ->prefix('transfers')
    ->group(function () {
        Route::post('/', 'transfer');
        Route::post('/bulk', 'transferBulk');
        Route::post('resolve-account', 'resolveAccount');
    });

Route::controller(WebhookController::class)
    ->prefix('webhooks')
    ->group(function () {
        Route::any('fincra', 'fincra');
    });

Route::controller(ExperimentController::class)
    ->prefix('experiments')
    ->group(function () {
        Route::get('banks', 'fetchBanks');
        Route::get('business-id', 'fetchBusinessId');
    });

Route::fallback(fn () => response()->not_found());
