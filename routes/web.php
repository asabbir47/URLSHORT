<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShortUrlController;

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

Route::get('/', [ShortUrlController::class, 'index'])->name('shorturl');
Route::get('/{short_url}',[ShortUrlController::class,'show'])->name('shorturl.show');
Route::post('/shorturl/store', [ShortUrlController::class, 'store'])->name('shorturl.store');

// Route::get('/dfg/{folder}/{shorturl}', [ShortUrlController::class, 'hola'])->name('shorturl.hola');
// Route::get('/home/{dwqbt}', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('/{folder?}/{shorturl}', [ShortUrlController::class, 'show'])->where('folder','.*')->name('shorturl.show');

Route::get('/{folder}/{h}', [ShortUrlController::class,'showWithFolder'])->where('folder', '.+')->name('shorturl.showWithFolder');