<?php

use Illuminate\Support\Facades\Route;
use Plugins\User\Api\Controllers\PingController;

Route::prefix('user')->group(function () {
    Route::get('/ping', [PingController::class, 'index']);
});