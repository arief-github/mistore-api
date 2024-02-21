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

    // products resource
    Route::apiResource('/products', App\Http\Controllers\Api\Web\ProductController::class, ['except' => ['create', 'store', 'edit','update', 'destroy'], 'as' => 'web']);

    // slider resource
    Route::apiResource('/sliders', App\Http\Controllers\Api\Web\SliderController::class, ['except' => ['create', 'store', 'edit','update', 'destroy'], 'as' => 'web']);
});

Route::prefix('customer')->group(function() {
    // route register
    Route::post('/register', [App\Http\Controllers\Api\Customer\RegisterController::class, 'store', ['as' => 'customer']]);

    // route login
    Route::post('/login', [App\Http\Controllers\Api\Customer\LoginController::class, 'index', ['as' => 'customer']]);

    // group route with middleware "auth:api_customers"
    Route::group(['middleware' => 'auth:api_customer'], function() {
        // data user/customer
        Route::get('/user', [App\Http\Controllers\Api\Customer\LoginController::class, 'getUser',['as' => 'customer']]);

        //refresh JWT token
        Route::get('/refresh', [App\Http\Controllers\Api\Customer\LoginController::class, 'refreshToken', ['as' => 'customer']]);

        // logout
        Route::post('/logout', [App\Http\Controllers\Api\Customer\LoginController::class, 'logout', ['as' => 'customer']]);

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Customer\DashboardController::class, 'index', ['as' => 'customer']]);
    });
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
        Route::post('/logout', [App\Http\Controllers\Api\Admin\LoginController::class,'logout', ['as' => 'admin']]);

        Route::get('/dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'index', ['as' => 'admin']]);

        // CRUD Users
        Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        //CRUD Categories
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        // CRUD Products
        Route::apiResource('/products', App\Http\Controllers\Api\Admin\ProductController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        // CRD Sliders
        Route::apiResource('/sliders', App\Http\Controllers\Api\Admin\SliderController::class, ['except' => ['create', 'show', 'edit', 'update'], 'as' => 'admin']);

        // read customers
        Route::get('/customers', [App\Http\Controllers\Api\Admin\CustomerController::class, 'index', ['as' => 'admin']]);
    });
});
