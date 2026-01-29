<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::middleware(['web'])->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.index');
        })->name('dashboard');
        Route::get('/dashboard/create', function () {
            return view('dashboard.create');
        })->name('dashboard.create');
    });
});

// GASS Routes
Route::prefix('gass')->name('gass.')->group(function () {
    Route::get('/', function () {
        return view('gass.index');
    })->name('index');

    Route::get('/create', function () {
        return view('gass.create');
    })->name('create');

    Route::get('/indicators', function () {
        return view('gass.indicators');
    })->name('indicators');

    Route::post('/store', function () {
        // Handle GASS PPA creation logic
        return redirect()->route('gass.index')->with('success', 'PPA created successfully!');
    })->name('store');

    Route::post('/indicators/store', function () {
        // Handle GASS Indicators creation logic
        return redirect()->route('gass.index')->with('success', 'Indicator created successfully!');
    })->name('indicators.store');
});
