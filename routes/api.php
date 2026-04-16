<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierController;

Route::get('suppliers/{id}/export', [SupplierController::class, 'export']);
Route::post('suppliers/{id}/import', [SupplierController::class, 'import']);
Route::apiResource('suppliers', SupplierController::class);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
