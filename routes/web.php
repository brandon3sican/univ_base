<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EnfController;
use App\Http\Controllers\GassController;
use App\Http\Controllers\LandsController;
use App\Http\Controllers\BiodiversityController;
use App\Http\Controllers\StoController;
use App\Http\Controllers\SoilconController;
use App\Http\Controllers\NraController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    })->name('home');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // API Routes (no auth required for PPAs)
    Route::get('/api/ppas', [GassController::class, 'getPpasByRecordType']);
    Route::get('/api/lands/ppas', [LandsController::class, 'getPpasByRecordType']);
    Route::get('/api/sto/ppas', [StoController::class, 'getPpasByRecordType']);
    Route::get('/api/enf/ppas', [EnfController::class, 'getPpasByRecordType']);
    Route::get('/api/biodiversity/ppas', [BiodiversityController::class, 'getPpasByRecordType']);
    Route::get('/api/soilcon/ppas', [SoilconController::class, 'getPpasByRecordType']);
    Route::get('/api/nra/ppas', [NraController::class, 'getPpasByRecordType']);

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.index');
        })->name('dashboard');

        // GASS Routes
        Route::get('/gass', [GassController::class, 'index'])->name('gass.index');
        Route::get('/gass/create', [GassController::class, 'create'])->name('gass.create');
        Route::post('/gass', [GassController::class, 'store'])->name('gass.store');
        Route::get('/gass/{id}', [GassController::class, 'show'])->name('gass.show');
        Route::get('/gass/{id}/edit', [GassController::class, 'edit'])->name('gass.edit');
        Route::put('/gass/{id}', [GassController::class, 'update'])->name('gass.update');
        Route::delete('/gass/{id}', [GassController::class, 'destroy'])->name('gass.destroy');

        // STO Routes
        Route::get('/sto', [StoController::class, 'index'])->name('sto.index');
        Route::get('/sto/create', [StoController::class, 'create'])->name('sto.create');
        Route::post('/sto', [StoController::class, 'store'])->name('sto.store');
        Route::get('/sto/{id}', [StoController::class, 'show'])->name('sto.show');
        Route::get('/sto/{id}/edit', [StoController::class, 'edit'])->name('sto.edit');
        Route::put('/sto/{id}', [StoController::class, 'update'])->name('sto.update');
        Route::delete('/sto/{id}', [StoController::class, 'destroy'])->name('sto.destroy');

        // ENF Routes
        Route::get('/enf', [EnfController::class, 'index'])->name('enf.index');
        Route::get('/enf/create', [EnfController::class, 'create'])->name('enf.create');
        Route::post('/enf', [EnfController::class, 'store'])->name('enf.store');
        Route::get('/enf/{id}', [EnfController::class, 'show'])->name('enf.show');
        Route::get('/enf/{id}/edit', [EnfController::class, 'edit'])->name('enf.edit');
        Route::put('/enf/{id}', [EnfController::class, 'update'])->name('enf.update');
        Route::delete('/enf/{id}', [EnfController::class, 'destroy'])->name('enf.destroy');

        // LANDS Routes
        Route::get('/lands', [LandsController::class, 'index'])->name('lands.index');
        Route::get('/lands/create', [LandsController::class, 'create'])->name('lands.create');
        Route::post('/lands', [LandsController::class, 'store'])->name('lands.store');
        Route::get('/lands/{id}', [LandsController::class, 'show'])->name('lands.show');
        Route::get('/lands/{id}/edit', [LandsController::class, 'edit'])->name('lands.edit');
        Route::put('/lands/{id}', [LandsController::class, 'update'])->name('lands.update');
        Route::delete('/lands/{id}', [LandsController::class, 'destroy'])->name('lands.destroy');

        // BIODIVERSITY Routes
        Route::get('/biodiversity', [BiodiversityController::class, 'index'])->name('biodiversity.index');
        Route::get('/biodiversity/create', [BiodiversityController::class, 'create'])->name('biodiversity.create');
        Route::post('/biodiversity', [BiodiversityController::class, 'store'])->name('biodiversity.store');
        Route::get('/biodiversity/{id}', [BiodiversityController::class, 'show'])->name('biodiversity.show');
        Route::get('/biodiversity/{id}/edit', [BiodiversityController::class, 'edit'])->name('biodiversity.edit');
        Route::put('/biodiversity/{id}', [BiodiversityController::class, 'update'])->name('biodiversity.update');
        Route::delete('/biodiversity/{id}', [BiodiversityController::class, 'destroy'])->name('biodiversity.destroy');

        // SOILCON Routes
        Route::get('/soilcon', [SoilconController::class, 'index'])->name('soilcon.index');
        Route::get('/soilcon/create', [SoilconController::class, 'create'])->name('soilcon.create');
        Route::post('/soilcon', [SoilconController::class, 'store'])->name('soilcon.store');
        Route::get('/soilcon/{id}', [SoilconController::class, 'show'])->name('soilcon.show');
        Route::get('/soilcon/{id}/edit', [SoilconController::class, 'edit'])->name('soilcon.edit');
        Route::put('/soilcon/{id}', [SoilconController::class, 'update'])->name('soilcon.update');
        Route::delete('/soilcon/{id}', [SoilconController::class, 'destroy'])->name('soilcon.destroy');

        // NRA Routes
        Route::get('/nra', [NraController::class, 'index'])->name('nra.index');
        Route::get('/nra/create', [NraController::class, 'create'])->name('nra.create');
        Route::post('/nra', [NraController::class, 'store'])->name('nra.store');
        Route::get('/nra/{id}', [NraController::class, 'show'])->name('nra.show');
        Route::get('/nra/{id}/edit', [NraController::class, 'edit'])->name('nra.edit');
        Route::put('/nra/{id}', [NraController::class, 'update'])->name('nra.update');
        Route::delete('/nra/{id}', [NraController::class, 'destroy'])->name('nra.destroy');
    });
});
