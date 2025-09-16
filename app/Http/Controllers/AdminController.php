<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $totalVentas = Venta::sum('total');
        $ventasHoy = Venta::whereDate('fecha_venta', today())->sum('total');
        $totalClientes = Cliente::activos()->count();
        $ventasRecientes = Venta::with(['cliente', 'productos.producto'])
                                ->orderBy('fecha_venta', 'desc')
                                ->limit(5)
                                ->get();

        return view('admin.dashboard', compact(
            'totalVentas',
            'ventasHoy',
            'totalClientes',
            'ventasRecientes'
        ));
    }

    public function productos()
    {
        $productos = Producto::all();
        return view('admin.productos', compact('productos'));
    }

    public function clientes()
    {
        $clientes = Cliente::with(['ventas'])->get();
        return view('admin.clientes', compact('clientes'));
    }

    public function reportes(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $ventas = Venta::with(['cliente', 'productos.producto'])
                       ->porFecha($fechaInicio, $fechaFin)
                       ->orderBy('fecha_venta', 'desc')
                       ->get();

        $totalVentas = $ventas->sum('total');
        $ventasPorTipo = $ventas->groupBy('tipo_cliente');
        $ventasMostrador = $ventasPorTipo->get('mostrador', collect())->sum('total');
        $ventasFactura = $ventasPorTipo->get('factura', collect())->sum('total');

        $productosMasVendidos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->join('ventas', 'venta_productos.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha_venta', [$fechaInicio, $fechaFin])
            ->select('productos.nombre', DB::raw('SUM(venta_productos.cantidad) as total_vendido'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->get();

        return view('admin.reportes', compact(
            'ventas',
            'totalVentas',
            'ventasMostrador',
            'ventasFactura',
            'productosMasVendidos',
            'fechaInicio',
            'fechaFin'
        ));
    }
}