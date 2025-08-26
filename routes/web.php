<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Invitation\InvitationController;

Route::redirect('/', 'dashboard');

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'login')->name('login')->middleware('guest');
    Route::post('login', 'doLogin')->name('auth.login');
    Route::post('logout', 'logout')->name('auth.logout');
});

Route::controller(IndexController::class)->middleware('auth')->group(function () {
    Route::get('dashboard', 'dashboard')->name('dashboard');
    // ⚠️ enlevé "invitation" ici car ce n’est pas IndexController qui doit le gérer
});

Route::controller(App\Http\Controllers\Invitation\InvitationController::class)->group(function () {
    Route::get('invitation', 'index')->name('invitation.index');
    Route::get('invitation/create', 'create')->name('invitation.create');
    Route::post('invitation', 'store')->name('invitation.store');
    Route::get('invitation/{unique_code}', 'show')->name('invitation.show');
});

