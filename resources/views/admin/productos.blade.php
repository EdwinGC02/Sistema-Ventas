@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-box me-2"></i>
                Gestión de Productos
            </h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Lista de Productos
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock Actual</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            <tr id="producto-row-{{ $producto->id }}">
                                <td>
                                    <span class="badge bg-secondary">{{ $producto->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-utensils text-primary me-2"></i>
                                        <strong>{{ $producto->nombre }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">
                                        ${{ number_format($producto->precio, 0) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $producto->stock > 10 ? 'bg-success' : ($producto->stock > 0 ? 'bg-warning' : 'bg-danger') }} fs-6" 
                                          id="stock-badge-{{ $producto->id }}">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="activo-{{ $producto->id }}"
                                               {{ $producto->activo ? 'checked' : '' }}
                                               onchange="toggleActivo({{ $producto->id }})">
                                        <label class="form-check-label" for="activo-{{ $producto->id }}">
                                            <span class="badge {{ $producto->activo ? 'bg-success' : 'bg-secondary' }}" 
                                                  id="estado-badge-{{ $producto->id }}">
                                                {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                onclick="mostrarModalStock({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->stock }})">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            Actualizar Stock
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Actualizar Stock -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Actualizar Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-stock">
                <div class="modal-body">
                    <input type="hidden" id="producto-id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Producto:</label>
                        <p class="form-control-plaintext" id="producto-nombre"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Stock Actual:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-info fs-6" id="stock-actual"></span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nuevo-stock" class="form-label fw-bold">Nuevo Stock:</label>
                        <input type="number" class="form-control" id="nuevo-stock" name="stock" 
                               min="0" max="9999" required>
                        <div class="form-text">
                            Ingrese la cantidad total de stock disponible
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-guardar-stock">
                        <i class="fas fa-save me-1"></i>
                        Actualizar Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function mostrarModalStock(id, nombre, stockActual) {
    $('#producto-id').val(id);
    $('#producto-nombre').text(nombre);
    $('#stock-actual').text(stockActual);
    $('#nuevo-stock').val(stockActual);
    $('#modalStock').modal('show');
}

function toggleActivo(productoId) {
    const checkbox = $(`#activo-${productoId}`);
    const badge = $(`#estado-badge-${productoId}`);
    
    checkbox.prop('disabled', true);
    
    $.ajax({
        url: `{{ url('api/productos') }}/${productoId}/toggle`,
        method: 'PATCH',
        success: function(response) {
            if (response.success) {
                if (response.activo) {
                    badge.removeClass('bg-secondary').addClass('bg-success').text('Activo');
                } else {
                    badge.removeClass('bg-success').addClass('bg-secondary').text('Inactivo');
                }
                showSuccess(response.message);
            }
        },
        error: function(xhr) {
            // Revertir el checkbox
            checkbox.prop('checked', !checkbox.prop('checked'));
            const message = xhr.responseJSON?.message || 'Error al cambiar el estado';
            showError(message);
        },
        complete: function() {
            checkbox.prop('disabled', false);
        }
    });
}

// Manejar formulario de stock
$('#form-stock').on('submit', function(e) {
    e.preventDefault();
    
    const productoId = $('#producto-id').val();
    const nuevoStock = parseInt($('#nuevo-stock').val());
    const btnGuardar = $('#btn-guardar-stock');
    
    if (isNaN(nuevoStock) || nuevoStock < 0) {
        showError('Ingrese un stock válido');
        return;
    }
    
    setLoadingButton(btnGuardar, true);
    
    $.ajax({
        url: `{{ url('api/productos') }}/${productoId}`,
        method: 'PUT',
        data: { stock: nuevoStock },
        success: function(response) {
            if (response.success) {
                // Actualizar la interfaz
                const stockBadge = $(`#stock-badge-${productoId}`);
                stockBadge.text(nuevoStock);
                
                // Cambiar color del badge según el stock
                stockBadge.removeClass('bg-success bg-warning bg-danger');
                if (nuevoStock > 10) {
                    stockBadge.addClass('bg-success');
                } else if (nuevoStock > 0) {
                    stockBadge.addClass('bg-warning');
                } else {
                    stockBadge.addClass('bg-danger');
                }
                
                $('#modalStock').modal('hide');
                showSuccess(response.message);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                handleValidationErrors(errors, '#form-stock');
                let errorMessage = 'Errores de validación:\n';
                Object.values(errors).forEach(errorArray => {
                    errorMessage += '• ' + errorArray[0] + '\n';
                });
                showError(errorMessage);
            } else {
                const message = xhr.responseJSON?.message || 'Error al actualizar el stock';
                showError(message);
            }
        },
        complete: function() {
            setLoadingButton(btnGuardar, false);
        }
    });
});

// Limpiar errores al cerrar modal
$('#modalStock').on('hidden.bs.modal', function() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
});
</script>
@endpush