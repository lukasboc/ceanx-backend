<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/product/create', [App\Http\Controllers\ProjectController::class, 'create'])->name('project.create');

Route::get('/subscription-checkout-classic', function (Request $request) {
    return $request->user()
        ->newSubscription('smplwrklg', 'price_1KzNDOEv6BP7l4DYkQWsO8Do')
        ->checkout([
            'success_url' => env('SPA_HOME_URL') . '/welcome',
            'cancel_url' => env('SPA_HOME_URL') . '/error',
            ]);
});

Route::get('/subscription-checkout-premium', function (Request $request) {
    return $request->user()
        ->newSubscription('smplwrklg', 'price_1KzNB7Ev6BP7l4DYlw5avlLr')
        ->checkout([
            'success_url' => env('SPA_HOME_URL') . '/welcome',
            'cancel_url' => env('SPA_HOME_URL') . '/error',
        ]);
});
