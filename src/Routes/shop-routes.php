<?php

use Illuminate\Support\Facades\Route;
use Webkul\AzamPay\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {

    /**
     * AzamPay payment routes
     */
    Route::get('/azampay-redirect', [PaymentController::class, 'redirect'])->name('azampay.process');

    Route::get('/azampay-success', [PaymentController::class, 'success'])->name('azampay.success');

    Route::post('/azampay-cancel', [PaymentController::class, 'failure'])->name('azampay.cancel');
});
