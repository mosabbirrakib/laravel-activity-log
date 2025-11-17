<?php

use Illuminate\Support\Facades\Route;
use AlMosabbirRakib\ActivityLog\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Activity Log API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for the Activity Log package.
|
*/

Route::get('/', [ActivityLogController::class, 'getLogs'])->name('activity-log.api.index');
Route::get('/types', [ActivityLogController::class, 'getTypes'])->name('activity-log.api.types');
Route::get('/stats', [ActivityLogController::class, 'getStats'])->name('activity-log.api.stats');
Route::get('/{id}', [ActivityLogController::class, 'show'])->name('activity-log.api.show');
Route::delete('/cleanup', [ActivityLogController::class, 'cleanup'])->name('activity-log.api.cleanup');

