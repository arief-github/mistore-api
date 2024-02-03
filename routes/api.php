<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('web')->group(function() {
    //categories resource
    Route::apiResource('/categories', App\Http\Controllers\Api\Web\CategoryController::class,['except' => ['create', 'store', 'edit','update', 'destroy'], 'as' => 'web']);
});

Route::prefix("admin")->group(function () {
    // route login
    Route::post("/login", [App\Http\Controllers\Api\Admin\LoginController::class, 'index', ['as' => 'admin']]);

    // group route with middleware "auth:api_admin"
    Route::group(['middleware' => 'auth:api_admin'], function () {
        // data user
        Route::get('/user', [App\Http\Controllers\Api\Admin\LoginController::class,'getUser', ['as' => 'admin']]);

        //refresh token jwt
        Route::get('/refresh', [App\Http\Controllers\Api\Admin\LoginController::class,'refreshToken', ['as' => 'admin']]);

        //logout
        Route::get('/logout', [App\Http\Controllers\Api\Admin\LoginController::class,'logout', ['as' => 'admin']]);

        Route::get('/dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'index', ['as' => 'admin']]);

        //CRUD Categories
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        // CRUD Products
        Route::apiResource('/products', App\Http\Controllers\Api\Admin\ProductController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);
    });
});
