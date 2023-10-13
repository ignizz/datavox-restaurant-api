<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaperTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('client')->get('test', function (Request $request) {
    return response()->json("Listo");
});

Route::middleware('client')->group(function () {
    /**
     * autenticate routes
     */
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
});

Route::middleware('auth:api')->group(function () {
    Route::any("profile",  [AuthController::class, "profile"])->name('api.profile');

    //users admin
    Route::any('/admin/users', [UserController::class, 'list'])->name('api.admin.users');
    Route::post('/admin/users/create', [UserController::class, 'store'])->name('api.admin.users.store');
    Route::get('/admin/users/edit/{user}', [UserController::class, 'edit'])->name('api.admin.users.edit');
    Route::put('/admin/users/edit/{user}', [UserController::class, 'update'])->name('api.admin.users.update');

});

//logout is allowed
Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');



