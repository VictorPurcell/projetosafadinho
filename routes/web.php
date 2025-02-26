<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotasController;
use App\Http\Controllers\TitulosController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\ConfiguracoesController;
use App\Http\Controllers\ClientesController;



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



Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'handleLogin'])->name('login.submit');
Route::get('/duo-callback', [AuthController::class, 'handleDuoCallback'])->name('duo.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'duo'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    
    Route::get('/notas/pdf', [NotasController::class, 'showPdf'])->name('notas.pdf-view');
    Route::get('/notas/emitir', [NotasController::class, 'emitir'])->name('notas.emitir');
    Route::get('/notas/integraçãofdc', [NotasController::class, 'fdc'])->name('integracao.fdc');


    Route::get('/notas', [NotasController::class, 'index'])->name('notas.index');
    Route::get('/titulos', [TitulosController::class, 'index'])->name('titulos.index');
    Route::get('/relatorios', [RelatoriosController::class, 'index'])->name('relatorios.index');
    Route::get('/configuracoes', [ConfiguracoesController::class, 'index'])->name('configuracoes.index');
    Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
