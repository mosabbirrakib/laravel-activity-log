<?php

use Illuminate\Support\Facades\Route;
use AlMosabbirRakib\ActivityLog\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Activity Log Web Routes
|--------------------------------------------------------------------------
|
| Here are the web routes for the Activity Log package.
|
*/

Route::get('/', [ActivityLogController::class, 'index'])->name('activity-log.index');

