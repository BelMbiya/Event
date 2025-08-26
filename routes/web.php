<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/','dashboard');

Route::controller(\App\Http\Controllers\Auth\AuthController::class)->group(function (){
    Route::get('login', 'login')->name('login')->middleware('guest');
    Route::post('login', 'doLogin')->name('auth.login');
    Route::post('logout', 'logout')->name('auth.logout');
});
Route::controller(\App\Http\Controllers\Admin\IndexController::class)->group(function (){
    Route::get('dashboard', 'dashboard')->name('dashboard');
})->middleware('auth');
