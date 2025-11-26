<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('show.register');
});

Route::get('/Register', [AuthController::class, 'ShowRegister'])->name('show.register');
Route::get('/Login', [AuthController::class, 'ShowLogin'])->name('show.login');

Route::post('/Login', [AuthController::class, 'Login'])->name('login');
Route::post('/Register', [AuthController::class, 'Register'])->name('register');
