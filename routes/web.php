<?php

use App\Modules\Dashboard\Http\Controllers\DashboardController;
use App\Modules\Parties\Http\Controllers\OccupancyController;
use App\Modules\Parties\Http\Controllers\PartyController;
use App\Modules\Portfolio\Http\Controllers\BuildingController;
use App\Modules\Portfolio\Http\Controllers\CountryController;
use App\Modules\Portfolio\Http\Controllers\PropertyController;
use App\Modules\Portfolio\Http\Controllers\UnitController;
use App\Modules\System\Http\Controllers\AuditController;
use App\Modules\Users\Http\Controllers\ProfileController;
use App\Modules\Users\Http\Controllers\RoleController;
use App\Modules\Users\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login, Passwort-Reset und 2FA-Routen registriert Laravel Fortify.

Route::post('/locale', [ProfileController::class, 'locale'])->name('locale');

// Die Übersicht liegt bewusst nicht auf "/": Laravel erkennt mit route:cache
// in einem Unterordner (/ImmoV/) die Wurzel nicht zuverlässig. Apache leitet
// /ImmoV/ per .htaccess direkt weiter; diese Route deckt alle anderen Fälle ab.
Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->middleware('permission:dashboard.view')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Portfolio: Land → Liegenschaft → Gebäude → Einheit
    Route::middleware('permission:portfolio.view')->group(function () {
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/create', [PropertyController::class, 'create'])->middleware('permission:portfolio.create')->name('properties.create');
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
        Route::get('/buildings/{building}', [BuildingController::class, 'show'])->name('buildings.show');
        Route::get('/units/{unit}', [UnitController::class, 'show'])->name('units.show');
    });

    Route::middleware('permission:portfolio.create')->group(function () {
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}/buildings/create', [BuildingController::class, 'create'])->name('buildings.create');
        Route::post('/properties/{property}/buildings', [BuildingController::class, 'store'])->name('buildings.store');
        Route::get('/buildings/{building}/units/create', [UnitController::class, 'create'])->name('units.create');
        Route::post('/buildings/{building}/units', [UnitController::class, 'store'])->name('units.store');
    });

    Route::middleware('permission:portfolio.update')->group(function () {
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::get('/buildings/{building}/edit', [BuildingController::class, 'edit'])->name('buildings.edit');
        Route::put('/buildings/{building}', [BuildingController::class, 'update'])->name('buildings.update');
        Route::get('/units/{unit}/edit', [UnitController::class, 'edit'])->name('units.edit');
        Route::put('/units/{unit}', [UnitController::class, 'update'])->name('units.update');
    });

    Route::middleware('permission:portfolio.delete')->group(function () {
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        Route::delete('/buildings/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
        Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');
    });

    // Parteien und Nutzungen
    Route::middleware('permission:parties.view')->group(function () {
        Route::get('/parties', [PartyController::class, 'index'])->name('parties.index');
        Route::get('/parties/create', [PartyController::class, 'create'])->middleware('permission:parties.create')->name('parties.create');
        Route::get('/parties/{party}', [PartyController::class, 'show'])->name('parties.show');
    });
    Route::middleware('permission:parties.create')->group(function () {
        Route::post('/parties', [PartyController::class, 'store'])->name('parties.store');
        Route::get('/units/{unit}/occupancies/create', [OccupancyController::class, 'create'])->name('occupancies.create');
        Route::post('/units/{unit}/occupancies', [OccupancyController::class, 'store'])->name('occupancies.store');
    });
    Route::middleware('permission:parties.update')->group(function () {
        Route::get('/parties/{party}/edit', [PartyController::class, 'edit'])->name('parties.edit');
        Route::put('/parties/{party}', [PartyController::class, 'update'])->name('parties.update');
        Route::get('/occupancies/{occupancy}/edit', [OccupancyController::class, 'edit'])->name('occupancies.edit');
        Route::put('/occupancies/{occupancy}', [OccupancyController::class, 'update'])->name('occupancies.update');
    });
    Route::middleware('permission:parties.delete')->group(function () {
        Route::delete('/parties/{party}', [PartyController::class, 'destroy'])->name('parties.destroy');
        Route::delete('/occupancies/{occupancy}', [OccupancyController::class, 'destroy'])->name('occupancies.destroy');
    });

    // Administration
    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/admin/countries', [CountryController::class, 'index'])->name('countries.index');
        Route::get('/admin/countries/create', [CountryController::class, 'create'])->name('countries.create');
        Route::post('/admin/countries', [CountryController::class, 'store'])->name('countries.store');
        Route::get('/admin/countries/{country}/edit', [CountryController::class, 'edit'])->name('countries.edit');
        Route::put('/admin/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
    });

    Route::middleware('permission:users.manage')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/admin/users/{user}/two-factor', [UserController::class, 'resetTwoFactor'])->name('users.two-factor.reset');
    });

    Route::middleware('permission:roles.manage')->group(function () {
        Route::get('/admin/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::put('/admin/roles', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::get('/admin/audit', [AuditController::class, 'index'])->middleware('permission:audit.view')->name('audits.index');
});
