@extends('layouts.app')

@section('title', 'Reportes de Ventas')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="fas fa-chart-bar me-2"></i>
            Reportes de Ventas
        </h2>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reportes') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="fecha_inicio" class="form-label fw-bold">Fecha Inicio:</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_fin" class="form-label fw-bold">Fecha Fin:</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="{{ $fechaFin }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                Filtrar
                            </button>
                            <a href="{{ route('admin.reportes') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Resumen Estadísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                <h4 class="fw-bold">${{ number_format($totalVentas, 0) }}</h4>
                <p class="mb-0">Total Ventas</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body text-center">
                <i class="fas fa-store fa-2x mb-3"></i>
                <h4 class="fw-bold">${{ number_format($ventasMostrador, 0) }}</h4>
                <p class="mb-0">Ventas Mostrador</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
            <div class="card-body text-center">
                <i class="fas fa-file-invoice fa-2x mb-3"></i>
                <h4 class="fw-bold">${{ number_format($ventasFactura, 0) }}</h4>
                <p class="mb-0">Ventas Facturadas</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-center">
                <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                <h4 class="fw-bold">{{ $ventas->count() }}</h4>
                <p class="mb-0">Total Transacciones</p>
            </div>
        </div>
    </div>
</div>

<!-- Productos Más Vendidos -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    Productos Más Vendidos
                </h5>
            </div>
            <div class="card-body">
                @if($productosMasVendidos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productosMasVendidos as $producto)
                                <tr>
                                    <td>
                                        <i class="fas fa-utensils text-primary me-2"></i>
                                        {{ $producto->nombre }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $producto->total_vendido }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                        <p>No hay datos de productos vendidos</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Distribución por Tipo de Cliente -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Distribución por Tipo de Cliente
                </h5>
            </div>
            <div class="card-body">
                @if($totalVentas > 0)
                    @php
                        $porcentajeMostrador = $totalVentas > 0 ? ($ventasMostrador / $totalVentas) * 100 : 0;
                        $porcentajeFactura = $totalVentas > 0 ? ($ventasFactura / $totalVentas) * 100 : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ventas de Mostrador</span>
                            <span>{{ number_format($porcentajeMostrador, 1) }}%</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $porcentajeMostrador }}%"></div>
                        </div>
                        <small class="text-muted">${{ number_format($ventasMostrador, 0) }}</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ventas Facturadas</span>
                            <span>{{ number_format($porcentajeFactura, 1) }}%</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $porcentajeFactura }}%"></div>
                        </div>
                        <small class="text-muted">${{ number_format($ventasFactura, 0) }}</small>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-chart-pie fa-2x mb-2"></i>
                        <p>No hay datos para mostrar</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detalle de Ventas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Detalle de Ventas
                    <span class="badge bg-primary ms-2">{{ $ventas->count() }} registros</span>
                </h5>
            </div>
            <div class="card-body">
                @if($ventas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Productos</th>
                                    <th>Subtotal</th>
                                    <th>IVA</th>
                                    <th>Total</th>
                                    <th>Factura</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventas as $venta)
                                <tr>
                                    <td>
                                        <div class="small">
                                            <strong>{{ $venta->fecha_venta->format('d/m/Y') }}</strong><br>
                                            <span class="text-muted">{{ $venta->fecha_venta->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $venta->nombre_cliente }}</strong>
                                    </td>
                                    <td>
                                        @if($venta->tipo_cliente === 'mostrador')
                                            <span class="badge bg-info">Mostrador</span>
                                        @else
                                            <span class="badge bg-success">Factura</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            @foreach($venta->productos as $item)
                                                <div class="mb-1">
                                                    {{ $item->producto->nombre }}
                                                    <span class="badge bg-secondary">{{ $item->cantidad }}</span>
                                                    <span class="text-muted">× ${{ number_format($item->precio_unitario, 0) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">${{ number_format($venta->subtotal, 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-warning">${{ number_format($venta->impuesto, 0) }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($venta->total, 0) }}</strong>
                                    </td>
                                    <td>
                                        @if($venta->numero_factura)
                                            <small class="text-primary">{{ $venta->numero_factura }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen al final de la tabla -->
                    <div class="row mt-4">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Resumen del Período</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>${{ number_format($ventas->sum('subtotal'), 0) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>IVA:</span>
                                        <span>${{ number_format($ventas->sum('impuesto'), 0) }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong class="text-success">${{ number_format($totalVentas, 0) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay ventas en el período seleccionado</h5>
                        <p class="text-muted">
                            Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} 
                            al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                        </p>
                        <a href="{{ route('pos.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Realizar Venta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection