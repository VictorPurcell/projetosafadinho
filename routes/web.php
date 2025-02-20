<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DuoController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::GET('/loginn', [AuthController::class, 'showLogin'])->name('login');

Route::POST('/loginn', [AuthController::class, 'handleLogin']);

Route::get('/duo-callback', [AuthController::class, 'handleDuoCallback'])->name('duo.callback');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Exemplo de rota protegida (só acessível se o usuário estiver autenticado e tiver passado no 2FA)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
