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
            <button type="button" class="btn btn-primary" onclick="mostrarModalProducto()">
                <i class="fas fa-plus me-2"></i>
                Nuevo Producto
            </button>
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
                                        <button type="button" class="btn btn-success btn-sm" 
                                                onclick="mostrarModalStock({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->stock }})">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            Stock
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                onclick="editarProducto({{ $producto->id }})">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="eliminarProducto({{ $producto->id }}, '{{ $producto->nombre }}')">
                                            <i class="fas fa-trash me-1"></i>
                                            Eliminar
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

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-producto-title">
                    <i class="fas fa-plus me-2"></i>
                    Nuevo Producto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-producto">
                <div class="modal-body">
                    <input type="hidden" id="producto-edit-id">
                    
                    <div class="mb-3">
                        <label for="producto-nombre" class="form-label fw-bold">
                            Nombre del Producto <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="producto-nombre" name="nombre" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="producto-precio" class="form-label fw-bold">
                                Precio <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="producto-precio" name="precio" 
                                       step="0.01" min="0" max="999999.99" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="producto-stock-inicial" class="form-label fw-bold">
                                Stock Inicial <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="producto-stock-inicial" name="stock" 
                                   min="0" max="9999" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-guardar-producto">
                        <i class="fas fa-save me-1"></i>
                        Guardar Producto
                    </button>
                </div>
            </form>
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
let productosData = @json($productos);
let modoEdicion = false;

function mostrarModalProducto(productoId = null) {
    modoEdicion = productoId !== null;
    
    // Limpiar formulario
    $('#form-producto')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    if (modoEdicion) {
        const producto = productosData.find(p => p.id === productoId);
        if (producto) {
            $('#modal-producto-title').html('<i class="fas fa-edit me-2"></i>Editar Producto');
            $('#producto-edit-id').val(producto.id);
            $('#producto-nombre').val(producto.nombre);
            $('#producto-precio').val(producto.precio);
            $('#producto-stock-inicial').val(producto.stock);
        }
    } else {
        $('#modal-producto-title').html('<i class="fas fa-plus me-2"></i>Nuevo Producto');
        $('#producto-edit-id').val('');
    }
    
    $('#modalProducto').modal('show');
}

function editarProducto(productoId) {
    mostrarModalProducto(productoId);
}

function eliminarProducto(productoId, nombreProducto) {
    showConfirm(
        '¿Eliminar Producto?',
        `¿Está seguro de que desea eliminar el producto "${nombreProducto}"? Esta acción no se puede deshacer.`,
        function() {
            $.ajax({
                url: `{{ url('api/productos') }}/${productoId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        // Remover fila de la tabla
                        $(`#producto-row-${productoId}`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Remover de datos locales
                        productosData = productosData.filter(p => p.id !== productoId);
                        
                        showSuccess(response.message);
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error al eliminar el producto';
                    showError(message);
                }
            });
        }
    );
}

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
                
                // Actualizar datos locales
                const productoIndex = productosData.findIndex(p => p.id === productoId);
                if (productoIndex !== -1) {
                    productosData[productoIndex].activo = response.activo;
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

// Manejar formulario de producto
$('#form-producto').on('submit', function(e) {
    e.preventDefault();
    
    const productoId = $('#producto-edit-id').val();
    const btnGuardar = $('#btn-guardar-producto');
    
    const data = {
        nombre: $('#producto-nombre').val().trim(),
        precio: parseFloat($('#producto-precio').val()),
        stock: parseInt($('#producto-stock-inicial').val())
    };
    
    setLoadingButton(btnGuardar, true);
    
    const url = modoEdicion ? `{{ url('api/productos') }}/${productoId}` : '{{ route("api.productos.store") }}';
    const method = modoEdicion ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function(response) {
            if (response.success) {
                if (modoEdicion) {
                    // Actualizar fila existente
                    actualizarFilaProducto(productoId, response.producto);
                    
                    // Actualizar datos locales
                    const productoIndex = productosData.findIndex(p => p.id === parseInt(productoId));
                    if (productoIndex !== -1) {
                        productosData[productoIndex] = { ...productosData[productoIndex], ...response.producto };
                    }
                } else {
                    // Agregar nueva fila
                    agregarFilaProducto(response.producto);
                    productosData.push(response.producto);
                }
                
                $('#modalProducto').modal('hide');
                showSuccess(response.message);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                handleValidationErrors(errors, '#form-producto');
            } else {
                const message = xhr.responseJSON?.message || 'Error al guardar el producto';
                showError(message);
            }
        },
        complete: function() {
            setLoadingButton(btnGuardar, false);
        }
    });
});

function actualizarFilaProducto(productoId, producto) {
    const fila = $(`#producto-row-${productoId}`);
    
    // Actualizar nombre
    fila.find('td:eq(1)').html(`
        <div class="d-flex align-items-center">
            <i class="fas fa-utensils text-primary me-2"></i>
            <strong>${producto.nombre}</strong>
        </div>
    `);
    
    // Actualizar precio
    fila.find('td:eq(2)').html(`
        <span class="text-success fw-bold">${formatNumber(producto.precio)}</span>
    `);
    
    // Actualizar stock
    const stockBadge = fila.find('td:eq(3) .badge');
    stockBadge.text(producto.stock);
    stockBadge.removeClass('bg-success bg-warning bg-danger');
    if (producto.stock > 10) {
        stockBadge.addClass('bg-success');
    } else if (producto.stock > 0) {
        stockBadge.addClass('bg-warning');
    } else {
        stockBadge.addClass('bg-danger');
    }
}

function agregarFilaProducto(producto) {
    const tbody = $('.table tbody');
    const stockClass = producto.stock > 10 ? 'bg-success' : (producto.stock > 0 ? 'bg-warning' : 'bg-danger');
    
    const nuevaFila = `
        <tr id="producto-row-${producto.id}">
            <td>
                <span class="badge bg-secondary">${producto.id}</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <i class="fas fa-utensils text-primary me-2"></i>
                    <strong>${producto.nombre}</strong>
                </div>
            </td>
            <td>
                <span class="text-success fw-bold">${formatNumber(producto.precio)}</span>
            </td>
            <td>
                <span class="badge ${stockClass} fs-6" id="stock-badge-${producto.id}">
                    ${producto.stock}
                </span>
            </td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" 
                           id="activo-${producto.id}" checked
                           onchange="toggleActivo(${producto.id})">
                    <label class="form-check-label" for="activo-${producto.id}">
                        <span class="badge bg-success" id="estado-badge-${producto.id}">
                            Activo
                        </span>
                    </label>
                </div>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success btn-sm" 
                            onclick="mostrarModalStock(${producto.id}, '${producto.nombre}', ${producto.stock})">
                        <i class="fas fa-plus-circle me-1"></i>
                        Stock
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" 
                            onclick="editarProducto(${producto.id})">
                        <i class="fas fa-edit me-1"></i>
                        Editar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" 
                            onclick="eliminarProducto(${producto.id}, '${producto.nombre}')">
                        <i class="fas fa-trash me-1"></i>
                        Eliminar
                    </button>
                </div>
            </td>
        </tr>
    `;
    tbody.append(nuevaFila);
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
                
                // Actualizar datos locales
                const productoIndex = productosData.findIndex(p => p.id === parseInt(productoId));
                if (productoIndex !== -1) {
                    productosData[productoIndex].stock = nuevoStock;
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

// Limpiar errores al cerrar modales
$('#modalProducto, #modalStock').on('hidden.bs.modal', function() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
});
</script>
@endpush