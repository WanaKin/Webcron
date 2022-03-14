<?php

use Illuminate\Support\Facades\Route;
use WanaKin\Webcron\Http\Controllers\WebcronController;
use WanaKin\Webcron\Http\Controllers\WorkerController;

if (config('webcron.scheduler.enabled')) {
    Route::get('/cron', WebcronController::class)->name('cron');
}

if (config('webcron.worker.enabled')) {
    Route::get('/worker', WorkerController::class)->name('worker');
}
