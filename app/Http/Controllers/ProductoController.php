<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function update(Request $request, Producto $producto)
    {
        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0|max:9999',
        ], [
            'stock.required' => 'El stock es requerido',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock no puede ser negativo',
            'stock.max' => 'El stock máximo es 9999',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $producto->update([
                'stock' => $request->stock
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado correctamente',
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'stock' => $producto->stock,
                    'precio' => $producto->precio
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el stock'
            ], 500);
        }
    }

    public function toggleActivo(Producto $producto)
    {
        try {
            $producto->update([
                'activo' => !$producto->activo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del producto actualizado',
                'activo' => $producto->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del producto'
            ], 500);
        }
    }
}