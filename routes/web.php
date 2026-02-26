<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NraController;
use App\Http\Controllers\StoController;
use App\Http\Controllers\GassController;

Route::middleware(['web'])->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.index');
        })->name('dashboard');
    });
});

// Dashboard Routes
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
    // Data Creation Routes
    Route::get('/create-program', [NraController::class, 'createProgram'])->name('create-program');
    Route::get('/create-project', [NraController::class, 'createProject'])->name('create-project');
    Route::get('/create-activity', [NraController::class, 'createActivity'])->name('create-activity');
    
    // Store Routes
    Route::post('/store-program', [NraController::class, 'storeProgram'])->name('store-program');
    Route::post('/store-project', [NraController::class, 'storeProject'])->name('store-project');
    Route::post('/store-activity', [NraController::class, 'storeActivity'])->name('store-activity');
});

// API Routes
Route::get('/api/programs/{systemType}', [NraController::class, 'getProgramsBySystemType'])->name('api.programs.by-system');
Route::get('/api/projects/{programId}', [NraController::class, 'getProjectsByProgram'])->name('api.projects.by-program');
Route::get('/api/activities/{projectId}', [NraController::class, 'getActivitiesByProject'])->name('api.activities.by-project');

// STO Routes
Route::prefix('sto')->name('sto.')->middleware(['auth'])->group(function () {
    Route::get('/', [StoController::class, 'index'])->name('index');
    Route::post('/store', [StoController::class, 'store'])->name('store');
    
    // CRUD Routes
    Route::get('/{id}', [StoController::class, 'show'])->name('show');
    Route::post('/{id}/update', [StoController::class, 'update'])->name('update');
    Route::delete('/{id}', [StoController::class, 'destroy'])->name('destroy');
    Route::post('/reorder', [StoController::class, 'reorder'])->name('reorder');
    Route::post('/move-up/{id}', [StoController::class, 'moveUp'])->name('moveUp');
    Route::post('/move-down/{id}', [StoController::class, 'moveDown'])->name('moveDown');
});

// NRA Routes
Route::prefix('nra')->name('nra.')->middleware(['auth'])->group(function () {
    Route::get('/', [NraController::class, 'index'])->name('index');
    Route::post('/store', [NraController::class, 'store'])->name('store');
    Route::post('/upload-to-nra-database', [NraController::class, 'uploadToNraDatabase'])->name('upload-to-nra-database');
    
    // CRUD Routes
    Route::get('/{id}', [NraController::class, 'show'])->name('show');
    Route::post('/{id}/update', [NraController::class, 'update'])->name('update');
    Route::delete('/{id}', [NraController::class, 'destroy'])->name('destroy');
    Route::post('/reorder', [NraController::class, 'reorder'])->name('reorder');
    Route::post('/move-up/{id}', [NraController::class, 'moveUp'])->name('moveUp');
    Route::post('/move-down/{id}', [NraController::class, 'moveDown'])->name('moveDown');
    
    // Update Routes
    Route::post('/program/{id}/update', [NraController::class, 'updateProgram'])->name('program.update');
    Route::post('/project/{id}/update', [NraController::class, 'updateProject'])->name('project.update');
    Route::post('/activity/{id}/update', [NraController::class, 'updateActivity'])->name('activity.update');
    
    // Helper route for getting parent activities
    Route::get('/get-parent-activities/{projectId}', [NraController::class, 'getParentActivities'])->name('get-parent-activities');
});

// GASS Routes
Route::prefix('gass')->name('gass.')->middleware(['auth'])->group(function () {
    Route::get('/', [GassController::class, 'index'])->name('index');
    Route::post('/store', [GassController::class, 'store'])->name('store');
    
    // CRUD Routes
    Route::get('/{id}', [GassController::class, 'show'])->name('show');
    Route::post('/{id}/update', [GassController::class, 'update'])->name('update');
    Route::delete('/{id}', [GassController::class, 'destroy'])->name('destroy');
    Route::post('/move-up/{id}', [GassController::class, 'moveUp'])->name('moveUp');
    Route::post('/move-down/{id}', [GassController::class, 'moveDown'])->name('moveDown');
    
    // Statistics and Export
    Route::get('/statistics', [GassController::class, 'statistics'])->name('statistics');
    Route::get('/export', [GassController::class, 'export'])->name('export');
});

