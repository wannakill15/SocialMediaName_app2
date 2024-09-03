<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/register', [UserController::class, 'register']);
Route::get('/forgot', [UserController::class, 'forgot']);
Route::get('/reset', [UserController::class, 'reset']);

Route::post('/register', [UserController::class, 'saveUser'])->name('auth.register');
Route::post('/login', [UserController::class, 'loginUser'])->name('auth.login');

Route::group(['middleware' => ['LoginCheck']], function(){
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/', [UserController::class, 'index']);
    Route::get('/logout', [UserController::class, 'logout'])->name('auth.logout');
});


