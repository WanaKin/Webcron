<?php

use Illuminate\Support\Facades\Route;
use WanaKin\Webcron\Http\Controllers\WebcronController;
use WanaKin\Webcron\Http\Controllers\WorkerController;

if (config('webcron.routes')) {
    Route::get('/cron', WebcronController::class)->name('cron');
    Route::get('/worker', WorkerController::class)->name('worker');
}
