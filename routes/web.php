<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);
Route::get('/', [HomeController::class, 'index'])->middleware('auth');
Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/filtered/products', [HomeController::class, 'filteredProducts'])->middleware('auth')->name('filtered_products');
Route::get('/products/with-image', [HomeController::class, 'productsWithImage'])->middleware('auth')->name('products_withImage');
Route::get('/products/without-image', [HomeController::class, 'productsWithoutImage'])->middleware('auth')->name('products_withoutImage');
Route::get('/notify', [HomeController::class, 'sendProductNotification'])->middleware('auth')->name('notify');
Route::get('/sync', [HomeController::class, 'sync'])->middleware('auth')->name('sync');
Route::post('/filter', [HomeController::class, 'filter'])->middleware('auth')->name('filter');
Route::post('/bulk_import', [HomeController::class, 'bulk_import'])->middleware('auth')->name('bulk_import');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth','is_admin']], function () {
	Route::get('/', [AdminController::class, 'index'])->name('home');
	Route::get('/dashboard', [AdminController::class, 'index'])->name('home');
	Route::get('/shop/add', [AdminController::class, 'create'])->name('shop.add');
	Route::post('/shop/add', [AdminController::class, 'store'])->name('shop.add.post');
	Route::get('/shop/view/{id}', [AdminController::class, 'show'])->name('shop.view');
	Route::get('/shop/edit/{id}', [AdminController::class, 'edit'])->name('shop.edit');
	Route::post('/shop/edit/{id}', [AdminController::class, 'update'])->name('shop.update');
	Route::delete('/shop/delete/{id}', [AdminController::class, 'delete'])->name('shop.delete');
	Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
	Route::post('/settings', [AdminController::class, 'settingsPost'])->name('settings.update');
});

Route::group(['prefix' => 'webhook', 'as' => 'webhook.'], function () {
	Route::get('/', [WebhookController::class,'index'])->name('index');
    Route::post('/product-create', [WebhookController::class, 'productCreate'])->name('product.create');
    Route::post('/product-update', [WebhookController::class, 'productUpdate'])->name('product.update');
    Route::post('/product-delete', [WebhookController::class, 'productDelete'])->name('product.delete');
    Route::post('/shop-update', [WebhookController::class, 'shopUpdate'])->name('shop.update');
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('storage:link');
});

