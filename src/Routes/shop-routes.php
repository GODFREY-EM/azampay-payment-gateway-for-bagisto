<?php

use Illuminate\Support\Facades\Route;
use Webkul\AzamPay\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {

    Route::get('/azampay-redirect', [PaymentController::class, 'redirect'])->name('azampay.process');

    Route::get('/azampay-success', [PaymentController::class, 'success'])->name('azampay.success');

    Route::get('/azampay-cancel', [PaymentController::class, 'cancel'])->name('azampay.cancel');
});
