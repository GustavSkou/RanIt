<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KudosController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});




Route::get('/register', [AuthController::class, 'ShowRegister'])->name('show.register');
Route::get('/login', [AuthController::class, 'ShowLogin'])->name('show.login');
Route::post('/login', [AuthController::class, 'Login'])->name('login');
Route::post('/register', [AuthController::class, 'Register'])->name('register');



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ActivityController::class, 'Index'])->name('dashboard');
    Route::get('/dashboard/{activity}', [ActivityController::class, 'Show'])->name('show');
    Route::get('/upload', [ActivityController::class, 'ShowUpload'])->name('show.upload');
    Route::get('/edit/{activity}', [ActivityController::class, 'ShowEdit'])->name('show.editActivity');

    Route::post('/upload', [ActivityController::class, 'Upload'])->name('upload');

    Route::get('/profile/{user}', [UserController::class, 'showProfile'])->name('profile');
    Route::get('/profile/edit/{user}', [UserController::class, 'showEdit'])->name('edit-profile');

    Route::get('/athletes', [UserController::class, 'index'])->name('user.index');

    Route::post('/follow', [UserController::class, 'follow'])->name('follow');
    Route::post('/unFollow', [UserController::class, 'unFollow'])->name('unFollow');

    Route::post('/kudos', [KudosController::class, 'kudos'])->name('kudos');
});
