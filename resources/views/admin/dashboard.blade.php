@extends('layouts.app')

@section('title', 'Dashboard - Administración')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="fas fa-chart-line me-2"></i>
            Dashboard - Panel de Control
        </h2>
    </div>
</div>

<!-- Estadísticas Principales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="admin-stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number">
                ${{ number_format($totalVentas, 0) }}
            </div>
            <div class="stat-label">
                Total Ventas Acumuladas
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="admin-stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-number">
                ${{ number_format($ventasHoy, 0) }}
            </div>
            <div class="stat-label">
                Ventas de Hoy
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="admin-stat-card" style="background: linear-gradient(135deg, #ff0008ff 0%, #eac674ff 100%);">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">
                {{ $totalClientes }}
            </div>
            <div class="stat-label">
                Clientes Registrados
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="admin-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-number">
                {{ $ventasRecientes->count() }}
            </div>
            <div class="stat-label">
                Ventas Recientes
            </div>
        </div>
    </div>
</div>

<!-- Ventas Recientes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Ventas Recientes
                </h5>
            </div>
            <div class="card-body">
                @if($ventasRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Factura</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventasRecientes as $venta)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $venta->fecha_venta->format('d/m/Y') }}<br>
                                            {{ $venta->fecha_venta->format('H:i') }}
                                        </small>
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
                                                <div>
                                                    {{ $item->producto->nombre }} 
                                                    <span class="text-muted">({{ $item->cantidad }})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            ${{ number_format($venta->total, 0) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($venta->numero_factura)
                                            <small class="text-muted">{{ $venta->numero_factura }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay ventas registradas aún</p>
                        <p class="small text-muted">
                            Ve a <strong>/pos</strong> para realizar la primera venta
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Enlaces Rápidos -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-link me-2"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.productos') }}" class="btn btn-outline-success btn-lg w-100 h-100">
                            <i class="fas fa-box fa-2x mb-2 d-block"></i>
                            Gestionar Productos
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.clientes') }}" class="btn btn-outline-info btn-lg w-100 h-100">
                            <i class="fas fa-users fa-2x mb-2 d-block"></i>
                            Gestionar Clientes
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.reportes') }}" class="btn btn-outline-warning btn-lg w-100 h-100">
                            <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                            Ver Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection