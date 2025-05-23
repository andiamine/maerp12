<?php

// routes/web.php - Routes personnalisées pour le dashboard

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;

Route::get('/', function () {
    return view('welcome');
});

// Routes admin personnalisées
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Export de données
    Route::get('/export/cabinets', [ExportController::class, 'exportCabinets'])
        ->name('export.cabinets');
    Route::get('/export/companies', [ExportController::class, 'exportCompanies'])
        ->name('export.companies');
    Route::get('/export/users', [ExportController::class, 'exportUsers'])
        ->name('export.users');

    // API pour les widgets
    Route::get('/api/stats', [DashboardController::class, 'getStats'])
        ->name('api.stats');
    Route::get('/api/charts/cabinets-by-city', [DashboardController::class, 'getCabinetsByCity'])
        ->name('api.charts.cabinets-by-city');
});
