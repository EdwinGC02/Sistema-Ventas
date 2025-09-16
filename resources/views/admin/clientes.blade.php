@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-users me-2"></i>
                Gestión de Clientes
            </h2>
            <button type="button" class="btn btn-primary" onclick="mostrarModalCliente()">
                <i class="fas fa-plus me-2"></i>
                Nuevo Cliente
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
                    Lista de Clientes
                </h5>
            </div>
            <div class="card-body">
                @if($clientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Contacto</th>
                                    <th>Compras</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientes as $cliente)
                                <tr id="cliente-row-{{ $cliente->id }}">
                                    <td>
                                        <span class="badge bg-secondary">{{ $cliente->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $cliente->nombre }}</strong>
                                                @if($cliente->direccion)
                                                    <br><small class="text-muted">{{ Str::limit($cliente->direccion, 30) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $cliente->documento }}</span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($cliente->email)
                                                <div><i class="fas fa-envelope me-1"></i>{{ $cliente->email }}</div>
                                            @endif
                                            @if($cliente->telefono)
                                                <div><i class="fas fa-phone me-1"></i>{{ $cliente->telefono }}</div>
                                            @endif
                                            @if(!$cliente->email && !$cliente->telefono)
                                                <span class="text-muted">Sin contacto</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div><strong>{{ $cliente->numero_compras }}</strong> compras</div>
                                            <div class="text-success">
                                                ${{ number_format($cliente->total_compras, 0) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="activo-cliente-{{ $cliente->id }}"
                                                   {{ $cliente->activo ? 'checked' : '' }}
                                                   onchange="toggleActivoCliente({{ $cliente->id }})">
                                            <label class="form-check-label" for="activo-cliente-{{ $cliente->id }}">
                                                <span class="badge {{ $cliente->activo ? 'bg-success' : 'bg-secondary' }}" 
                                                      id="estado-cliente-badge-{{ $cliente->id }}">
                                                    {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    onclick="editarCliente({{ $cliente->id }})">
                                                <i class="fas fa-edit me-1"></i>
                                                Editar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay clientes registrados</p>
                        <button type="button" class="btn btn-primary" onclick="mostrarModalCliente()">
                            <i class="fas fa-plus me-2"></i>
                            Registrar Primer Cliente
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-cliente-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Nuevo Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-cliente">
                <div class="modal-body">
                    <input type="hidden" id="cliente-id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente-nombre" class="form-label fw-bold">
                                Nombre Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="cliente-nombre" name="nombre" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cliente-documento" class="form-label fw-bold">
                                Documento <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="cliente-documento" name="documento" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente-email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="cliente-email" name="email">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cliente-telefono" class="form-label fw-bold">Teléfono</label>
                            <input type="tel" class="form-control" id="cliente-telefono" name="telefono">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cliente-direccion" class="form-label fw-bold">Dirección</label>
                        <textarea class="form-control" id="cliente-direccion" name="direccion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-guardar-cliente">
                        <i class="fas fa-save me-1"></i>
                        Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let clientesData = @json($clientes);
let modoEdicion = false;

function mostrarModalCliente(clienteId = null) {
    modoEdicion = clienteId !== null;
    
    // Limpiar formulario
    $('#form-cliente')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    if (modoEdicion) {
        const cliente = clientesData.find(c => c.id === clienteId);
        if (cliente) {
            $('#modal-cliente-title').html('<i class="fas fa-user-edit me-2"></i>Editar Cliente');
            $('#cliente-id').val(cliente.id);
            $('#cliente-nombre').val(cliente.nombre);
            $('#cliente-documento').val(cliente.documento);
            $('#cliente-email').val(cliente.email || '');
            $('#cliente-telefono').val(cliente.telefono || '');
            $('#cliente-direccion').val(cliente.direccion || '');
        }
    } else {
        $('#modal-cliente-title').html('<i class="fas fa-user-plus me-2"></i>Nuevo Cliente');
        $('#cliente-id').val('');
    }
    
    $('#modalCliente').modal('show');
}

function editarCliente(clienteId) {
    mostrarModalCliente(clienteId);
}

function toggleActivoCliente(clienteId) {
    const checkbox = $(`#activo-cliente-${clienteId}`);
    const badge = $(`#estado-cliente-badge-${clienteId}`);
    
    checkbox.prop('disabled', true);
    
    $.ajax({
        url: `{{ url('api/clientes') }}/${clienteId}/toggle`,
        method: 'PATCH',
        success: function(response) {
            if (response.success) {
                if (response.activo) {
                    badge.removeClass('bg-secondary').addClass('bg-success').text('Activo');
                } else {
                    badge.removeClass('bg-success').addClass('bg-secondary').text('Inactivo');
                }
                
                // Actualizar datos locales
                const clienteIndex = clientesData.findIndex(c => c.id === clienteId);
                if (clienteIndex !== -1) {
                    clientesData[clienteIndex].activo = response.activo;
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

// Manejar formulario de cliente
$('#form-cliente').on('submit', function(e) {
    e.preventDefault();
    
    const clienteId = $('#cliente-id').val();
    const btnGuardar = $('#btn-guardar-cliente');
    
    const data = {
        nombre: $('#cliente-nombre').val().trim(),
        documento: $('#cliente-documento').val().trim(),
        email: $('#cliente-email').val().trim(),
        telefono: $('#cliente-telefono').val().trim(),
        direccion: $('#cliente-direccion').val().trim()
    };
    
    setLoadingButton(btnGuardar, true);
    
    const url = modoEdicion ? `{{ url('api/clientes') }}/${clienteId}` : '{{ route("api.clientes.store") }}';
    const method = modoEdicion ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function(response) {
            if (response.success) {
                if (modoEdicion) {
                    // Actualizar fila existente
                    actualizarFilaCliente(clienteId, response.cliente);
                    
                    // Actualizar datos locales
                    const clienteIndex = clientesData.findIndex(c => c.id === parseInt(clienteId));
                    if (clienteIndex !== -1) {
                        clientesData[clienteIndex] = { ...clientesData[clienteIndex], ...response.cliente };
                    }
                } else {
                    // Agregar nueva fila
                    const nuevoCliente = { ...response.cliente, activo: true, numero_compras: 0, total_compras: 0 };
                    agregarFilaCliente(nuevoCliente);
                    clientesData.push(nuevoCliente);
                    
                    // Si era la primera fila, recargar para quitar el mensaje de vacío
                    if (clientesData.length === 1) {
                        location.reload();
                        return;
                    }
                }
                
                $('#modalCliente').modal('hide');
                showSuccess(response.message);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                handleValidationErrors(errors, '#form-cliente');
            } else {
                const message = xhr.responseJSON?.message || 'Error al guardar el cliente';
                showError(message);
            }
        },
        complete: function() {
            setLoadingButton(btnGuardar, false);
        }
    });
});

function actualizarFilaCliente(clienteId, cliente) {
    const fila = $(`#cliente-row-${clienteId}`);
    
    // Actualizar nombre y dirección
    const nombreCol = fila.find('td:eq(1)');
    let nombreHtml = `
        <div class="d-flex align-items-center">
            <i class="fas fa-user text-primary me-2"></i>
            <div>
                <strong>${cliente.nombre}</strong>
    `;
    if (cliente.direccion) {
        nombreHtml += `<br><small class="text-muted">${cliente.direccion.substring(0, 30)}${cliente.direccion.length > 30 ? '...' : ''}</small>`;
    }
    nombreHtml += `</div></div>`;
    nombreCol.html(nombreHtml);
    
    // Actualizar documento
    fila.find('td:eq(2)').html(`<span class="badge bg-info">${cliente.documento}</span>`);
    
    // Actualizar contacto
    let contactoHtml = '<div class="small">';
    if (cliente.email) {
        contactoHtml += `<div><i class="fas fa-envelope me-1"></i>${cliente.email}</div>`;
    }
    if (cliente.telefono) {
        contactoHtml += `<div><i class="fas fa-phone me-1"></i>${cliente.telefono}</div>`;
    }
    if (!cliente.email && !cliente.telefono) {
        contactoHtml += '<span class="text-muted">Sin contacto</span>';
    }
    contactoHtml += '</div>';
    fila.find('td:eq(3)').html(contactoHtml);
}

function agregarFilaCliente(cliente) {
    const tbody = $('.table tbody');
    const nuevaFila = `
        <tr id="cliente-row-${cliente.id}">
            <td>
                <span class="badge bg-secondary">${cliente.id}</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user text-primary me-2"></i>
                    <div>
                        <strong>${cliente.nombre}</strong>
                        ${cliente.direccion ? `<br><small class="text-muted">${cliente.direccion.substring(0, 30)}${cliente.direccion.length > 30 ? '...' : ''}</small>` : ''}
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-info">${cliente.documento}</span>
            </td>
            <td>
                <div class="small">
                    ${cliente.email ? `<div><i class="fas fa-envelope me-1"></i>${cliente.email}</div>` : ''}
                    ${cliente.telefono ? `<div><i class="fas fa-phone me-1"></i>${cliente.telefono}</div>` : ''}
                    ${!cliente.email && !cliente.telefono ? '<span class="text-muted">Sin contacto</span>' : ''}
                </div>
            </td>
            <td>
                <div class="small">
                    <div><strong>0</strong> compras</div>
                    <div class="text-success">$0</div>
                </div>
            </td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" 
                           id="activo-cliente-${cliente.id}" checked
                           onchange="toggleActivoCliente(${cliente.id})">
                    <label class="form-check-label" for="activo-cliente-${cliente.id}">
                        <span class="badge bg-success" id="estado-cliente-badge-${cliente.id}">
                            Activo
                        </span>
                    </label>
                </div>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary btn-sm" 
                            onclick="editarCliente(${cliente.id})">
                        <i class="fas fa-edit me-1"></i>
                        Editar
                    </button>
                </div>
            </td>
        </tr>
    `;
    tbody.append(nuevaFila);
}

// Limpiar errores al cerrar modal
$('#modalCliente').on('hidden.bs.modal', function() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
});
</script>
@endpush