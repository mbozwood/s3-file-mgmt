<?php

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::resource('files', 'FileController')->only(['index','create','store','show','destroy'])->middleware('auth');
Route::resource('admin', 'AdminController')->only(['index','show','destroy'])->middleware('admin');

Route::get('set-up-admin', function () {
    if(\App\Models\User::query()->count() == 1) {
        \App\Models\User::query()->first()->update(['is_admin' => 1]);
        return redirect()->route('dashboard');
    }
})->middleware('auth');

require __DIR__.'/auth.php';
