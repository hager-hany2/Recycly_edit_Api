<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryControllerResource;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilterCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PaymentController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'ChangeLang'], function () {

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegisterController::class, 'register'])->name('auth.register');
        Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
    });



    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::apiResource('categories', CategoryControllerResource::class);
        Route::get('/categories/store', [CategoryControllerResource::class, 'store']);
        Route::post('products', [ProductController::class, 'store'])->name('admin.products.store');
    });

    // Payment Routes
    Route::prefix('vodafone')->group(function () {
        Route::post('/callback', [PaymentController::class, 'handleCallback'])->name('vodafone.callback');
        Route::post('/', [PaymentController::class, 'store'])->name('vodafone.store');
    });
    // Order Routes
    Route::post('/order/{id}', [OrderController::class, 'store'])->name('order.store');
});


// Profile Routes
Route::prefix('profile')->group(function () {
    Route::get('/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
});
// Public Routes
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/categories/index', [CategoryControllerResource::class, 'index']);
Route::get('/products/index', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories/products/{category_id}', [FilterCategoryController::class, 'filterCategory'])->name('categories.products.filter');





