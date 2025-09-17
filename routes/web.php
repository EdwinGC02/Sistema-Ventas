<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;

// Redirigir la raíz al POS
Route::get('/', function () {
    return redirect('/pos');
});

// Rutas POS
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos/venta', [PosController::class, 'procesarVenta'])->name('pos.procesar-venta');
Route::get('/pos/producto/{id}', [PosController::class, 'obtenerProducto'])->name('pos.obtener-producto');

// Rutas Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/productos', [AdminController::class, 'productos'])->name('productos');
    Route::get('/clientes', [AdminController::class, 'clientes'])->name('clientes');
    Route::get('/reportes', [AdminController::class, 'reportes'])->name('reportes');
});

// API Routes para AJAX
Route::prefix('api')->name('api.')->group(function () {
    // Productos - CRUD completo
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::patch('/productos/{producto}/toggle', [ProductoController::class, 'toggleActivo'])->name('productos.toggle');
    
    // Clientes
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('api.clientes.destroy');
    Route::patch('/clientes/{cliente}/toggle', [ClienteController::class, 'toggleActivo'])->name('clientes.toggle');
});