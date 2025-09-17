<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:2',
            'precio' => 'required|numeric|min:0|max:999999.99',
            'stock' => 'required|integer|min:0|max:9999',
        ], [
            'nombre.required' => 'El nombre es requerido',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'precio.required' => 'El precio es requerido',
            'precio.numeric' => 'El precio debe ser un número válido',
            'precio.min' => 'El precio no puede ser negativo',
            'precio.max' => 'El precio máximo es 999,999.99',
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
            $producto = Producto::create([
                'nombre' => $request->nombre,
                'precio' => $request->precio,
                'stock' => $request->stock,
                'activo' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'stock' => $producto->stock,
                    'activo' => $producto->activo
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto'
            ], 500);
        }
    }

    public function update(Request $request, Producto $producto)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255|min:2',
            'precio' => 'sometimes|required|numeric|min:0|max:999999.99',
            'stock' => 'sometimes|required|integer|min:0|max:9999',
        ], [
            'nombre.required' => 'El nombre es requerido',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'precio.required' => 'El precio es requerido',
            'precio.numeric' => 'El precio debe ser un número válido',
            'precio.min' => 'El precio no puede ser negativo',
            'precio.max' => 'El precio máximo es 999,999.99',
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
            // Si solo se está actualizando el stock (petición desde modal stock)
            if ($request->has('stock') && count($request->all()) === 2) { // stock + _token
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
            }

            // Actualización completa del producto
            $producto->update($request->only(['nombre', 'precio', 'stock']));

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'stock' => $producto->stock,
                    'activo' => $producto->activo
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto'
            ], 500);
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            // Verificar si el producto tiene ventas asociadas
            $tieneVentas = $producto->ventaProductos()->exists();
            
            if ($tieneVentas) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el producto porque tiene ventas asociadas'
                ], 400);
            }

            $nombreProducto = $producto->nombre;
            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => "Producto '{$nombreProducto}' eliminado exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto'
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