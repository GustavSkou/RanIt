<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});




Route::get('/register', [AuthController::class, 'ShowRegister'])->name('show.register');
Route::get('/login', [AuthController::class, 'ShowLogin'])->name('show.login');
Route::post('/login', [AuthController::class, 'Login'])->name('login');
Route::post('/register', [AuthController::class, 'Register'])->name('register');



Route::get('/dashboard', [ActivityController::class, 'Index'])->name('dashboard');
Route::get('/dashboard/{activity}', [ActivityController::class, 'Show'])->name('show');
Route::get('/upload', [ActivityController::class, 'ShowUpload'])->name('show.upload');
Route::get('/edit/{activity}', [ActivityController::class, 'ShowEdit'])->name('show.editActivity');

Route::post('/upload', [ActivityController::class, 'Upload'])->name('upload');
