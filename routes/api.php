<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth;
use App\Http\Controllers\User;
use App\Http\Controllers\Registration;
use App\Http\Controllers\Account;
use App\Http\Controllers\Application;
use App\Http\Controllers\IncomingMessage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'checkTokenExpiry'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user'], function () {
    Route::post('/login', [Auth\LoginController::class, 'index']);
    Route::post('/create', [User\UserController::class, 'store']);

    Route::group(['middleware' => ['auth:sanctum', 'checkTokenExpiry']], function () {
        Route::get('/', [User\UserController::class, 'index']);
        Route::get('/show/{id}', [User\UserController::class, 'show']);
        Route::post('/update/{id}', [User\UserController::class, 'update']);
        Route::post('/logout', [Auth\LoginController::class, 'logout']);
    });
});

Route::group(['prefix' => 'registration', 'middleware' => 'auth:sanctum', 'checkTokenExpiry'], function () {
    Route::get('/', [Registration\RegistrationController::class, 'index']);
    Route::get('/show/{id}', [Registration\RegistrationController::class, 'show']);
    Route::post('/create', [Registration\RegistrationController::class, 'store']);
    Route::post('/update/{id}', [Registration\RegistrationController::class, 'update']);
    Route::get('/dashboard', [Registration\RegistrationController::class, 'dashboard']);
});

Route::group(['prefix' => 'account', 'middleware' => 'auth:sanctum', 'checkTokenExpiry'], function () {
    Route::get('/{login_id}', [Account\AccountController::class, 'index']);
    Route::get('/show/{login_id}/{account_number}', [Account\AccountController::class, 'show']);
    Route::post('/create', [Account\AccountController::class, 'store']);
    Route::post('/update/{id}', [Account\AccountController::class, 'update']);
    Route::get('/dashboard/data', [Account\AccountController::class, 'dashboard']);
});

Route::group(['prefix' => 'application', 'middleware' => 'auth:sanctum', 'checkTokenExpiry'], function () {
    Route::get('/{login_id}', [Application\ApplicationController::class, 'index']);
    Route::get('/show/{login_id}/{acct_no}/{trace_id}', [Application\ApplicationController::class, 'show']);
    Route::post('/create', [Application\ApplicationController::class, 'store']);
    Route::post('/update/{id}', [Application\ApplicationController::class, 'update']);
    Route::get('/dashboard/data', [Application\ApplicationController::class, 'dashboard']);
});

Route::group(['prefix' => 'incoming-message', 'middleware' => 'auth:sanctum', 'checkTokenExpiry'], function () {
    Route::get('/', [IncomingMessage\IncomingMessageController::class, 'index']);
    Route::get('/show/{incomingMessage}', [IncomingMessage\IncomingMessageController::class, 'show']);
    Route::get('/dashboard', [IncomingMessage\IncomingMessageController::class, 'dashboard']);
});
