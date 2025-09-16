<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    public function index()
    {
        $productos = Producto::activos()->conStock()->get();
        $clientes = Cliente::activos()->get();
        return view('pos.index', compact('productos', 'clientes'));
    }

    public function procesarVenta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_cliente' => 'required|in:mostrador,factura',
            'cliente_id' => 'required_if:tipo_cliente,factura|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1|max:100',
            'observaciones' => 'nullable|string|max:500'
        ], [
            'tipo_cliente.required' => 'Debe seleccionar el tipo de cliente',
            'tipo_cliente.in' => 'Tipo de cliente inválido',
            'cliente_id.required_if' => 'Debe seleccionar un cliente para facturación',
            'cliente_id.exists' => 'Cliente no válido',
            'productos.required' => 'Debe agregar al menos un producto',
            'productos.array' => 'Formato de productos inválido',
            'productos.min' => 'Debe agregar al menos un producto',
            'productos.*.id.required' => 'ID de producto requerido',
            'productos.*.id.exists' => 'Producto no válido',
            'productos.*.cantidad.required' => 'Cantidad requerida para todos los productos',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'productos.*.cantidad.min' => 'La cantidad mínima es 1',
            'productos.*.cantidad.max' => 'La cantidad máxima es 100 por producto',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar stock disponible
        $productosVenta = [];
        $subtotal = 0;

        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            
            if (!$producto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            if (!$producto->activo) {
                return response()->json([
                    'success' => false,
                    'message' => "El producto {$producto->nombre} no está disponible"
                ], 400);
            }

            if (!$producto->tieneStock($productoData['cantidad'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}"
                ], 400);
            }

            $subtotalProducto = $producto->precio * $productoData['cantidad'];
            $subtotal += $subtotalProducto;

            $productosVenta[] = [
                'producto' => $producto,
                'cantidad' => $productoData['cantidad'],
                'precio_unitario' => $producto->precio,
                'subtotal' => $subtotalProducto
            ];
        }

        // Validar cliente si es venta con factura
        $cliente = null;
        if ($request->tipo_cliente === 'factura') {
            $cliente = Cliente::find($request->cliente_id);
            if (!$cliente || !$cliente->activo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no válido o inactivo'
                ], 400);
            }
        }

        // Calcular impuestos (19% IVA)
        $impuesto = $subtotal * 0.19;
        $total = $subtotal + $impuesto;

        // Procesar la venta en transacción
        DB::beginTransaction();
        try {
            // Crear la venta
            $venta = new Venta([
                'cliente_id' => $request->tipo_cliente === 'factura' ? $cliente->id : null,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'tipo_cliente' => $request->tipo_cliente,
                'numero_factura' => $request->tipo_cliente === 'factura' ? Venta::generarNumeroFactura() : null,
                'observaciones' => $request->observaciones,
                'fecha_venta' => now()
            ]);
            $venta->save();

            // Crear los detalles de la venta y reducir stock
            foreach ($productosVenta as $productoVenta) {
                VentaProducto::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $productoVenta['producto']->id,
                    'cantidad' => $productoVenta['cantidad'],
                    'precio_unitario' => $productoVenta['precio_unitario'],
                    'subtotal' => $productoVenta['subtotal']
                ]);

                // Reducir stock
                $productoVenta['producto']->reducirStock($productoVenta['cantidad']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta' => [
                    'id' => $venta->id,
                    'numero_factura' => $venta->numero_factura,
                    'total' => $venta->total,
                    'cliente' => $venta->nombre_cliente,
                    'fecha' => $venta->fecha_venta->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerProducto($id)
    {
        $producto = Producto::activos()->conStock()->find($id);
        
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado o sin stock'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'stock' => $producto->stock
            ]
        ]);
    }
}