<?php

use Illuminate\Support\Facades\Route;
use admin\commissions\Controllers\CommissionManagerController;

Route::name('admin.')->middleware(['web','admin.auth'])->group(function () {     
    Route::resource('commissions', CommissionManagerController::class);
    Route::get('admin/commissions/fetch-options', [CommissionManagerController::class, 'fetchOptions'])->name('commissions.fetchOptions');
    Route::post('admin/commissions/updateStatus', [CommissionManagerController::class, 'updateStatus'])->name('commissions.updateStatus');
    // Add more commission-specific routes as needed
});
