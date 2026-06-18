<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

Route::post('/ventas/sync', [SyncController::class, 'syncSale']);