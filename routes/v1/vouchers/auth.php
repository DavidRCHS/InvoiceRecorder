<?php

use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use App\Http\Controllers\Vouchers\GetVoucherSummaryHandler;
use App\Http\Controllers\Vouchers\DeleteVoucherHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::post('/', StoreVouchersHandler::class);
        Route::get('/summary', GetVoucherSummaryHandler::class);
        Route::delete('/{id}', DeleteVoucherHandler::class);
    }
);
