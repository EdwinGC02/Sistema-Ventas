@extends('layouts.app')

@section('title', 'Punto de Venta')

@section('content')
<div class="row">
    <!-- Productos -->
    <div class="col-lg-8 col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Productos Disponibles
                </h5>
            </div>
            <div class="card-body">
                <div class="row" id="productos-container">
                    @foreach($productos as $producto)
                    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                        <div class="card pos-product-card h-100" data-producto-id="{{ $producto->id }}" data-precio="{{ $producto->precio }}" data-stock="{{ $producto->stock }}">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-utensils text-primary" style="font-size: 2.5rem;"></i>
                                </div>
                                <h6 class="card-title fw-bold">{{ $producto->nombre }}</h6>
                                <p class="card-text text-muted mb-2">
                                    Stock: <span class="badge bg-info">{{ $producto->stock }}</span>
                                </p>
                                <p class="card-text">
                                    <span class="h5 text-success fw-bold">${{ number_format($producto->precio, 0) }}</span>
                                </p>
                                <button class="btn btn-primary btn-sm w-100" onclick="agregarProducto({{ $producto->id }})">
                                    <i class="fas fa-plus me-1"></i>
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Carrito de Compras -->
    <div class="col-lg-4 col-md-5">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Carrito de Compras
                    <span class="badge bg-light text-dark ms-2" id="cart-count">0</span>
                </h5>
            </div>
            <div class="card-body">
                <!-- Tipo de Cliente -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo de Cliente:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="tipo_mostrador" value="mostrador" checked>
                        <label class="form-check-label" for="tipo_mostrador">
                            Cliente de Mostrador
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="tipo_factura" value="factura">
                        <label class="form-check-label" for="tipo_factura">
                            Cliente con Factura
                        </label>
                    </div>
                </div>

                <!-- Selección de Cliente -->
                <div class="mb-3" id="cliente-section" style="display: none;">
                    <label for="cliente_id" class="form-label fw-bold">Cliente:</label>
                    <select class="form-select" name="cliente_id" id="cliente_id">
                        <option value="">Seleccionar cliente...</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->documento }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Items del Carrito -->
                <div class="mb-3">
                    <div id="cart-items">
                        <div class="text-center text-muted py-4" id="empty-cart">
                            <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                            <p>El carrito está vacío</p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="mb-3">
                    <label for="observaciones" class="form-label fw-bold">Observaciones:</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales (opcional)"></textarea>
                </div>

                <!-- Totales -->
                <div class="total-section mb-3" id="totales-section" style="display: none;">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>IVA (19%):</span>
                        <span id="impuesto">$0</span>
                    </div>
                    <hr class="my-2 border-light">
                    <div class="d-flex justify-content-between">
                        <span class="h5 fw-bold">Total:</span>
                        <span class="h5 fw-bold" id="total">$0</span>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" id="btn-procesar-venta" disabled>
                        <i class="fas fa-cash-register me-2"></i>
                        Procesar Venta
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-limpiar-carrito" disabled>
                        <i class="fas fa-trash me-2"></i>
                        Limpiar Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let carrito = [];
let productos = @json($productos);

// Inicializar eventos
$(document).ready(function() {
    // Cambio de tipo de cliente
    $('input[name="tipo_cliente"]').change(function() {
        if ($(this).val() === 'factura') {
            $('#cliente-section').slideDown();
        } else {
            $('#cliente-section').slideUp();
            $('#cliente_id').val('');
        }
    });

    // Procesar venta
    $('#btn-procesar-venta').click(function() {
        procesarVenta();
    });

    // Limpiar carrito
    $('#btn-limpiar-carrito').click(function() {
        showConfirm('¿Limpiar carrito?', '¿Está seguro de que desea limpiar el carrito?', function() {
            limpiarCarrito();
        });
    });
});

function agregarProducto(productoId) {
    let producto = productos.find(p => p.id === productoId);
    if (!producto) {
        showError('Producto no encontrado');
        return;
    }

    // Verificar stock
    let itemExistente = carrito.find(item => item.id === productoId);
    let cantidadActual = itemExistente ? itemExistente.cantidad : 0;
    
    if (cantidadActual >= producto.stock) {
        showError(`Stock insuficiente para ${producto.nombre}. Disponible: ${producto.stock}`);
        return;
    }

    if (itemExistente) {
        itemExistente.cantidad++;
        itemExistente.subtotal = itemExistente.precio * itemExistente.cantidad;
    } else {
        carrito.push({
            id: productoId,
            nombre: producto.nombre,
            precio: parseFloat(producto.precio),
            cantidad: 1,
            stock: producto.stock,
            subtotal: parseFloat(producto.precio)
        });
    }

    actualizarCarrito();
}

function cambiarCantidad(productoId, nuevaCantidad) {
    let item = carrito.find(item => item.id === productoId);
    if (!item) return;

    if (nuevaCantidad <= 0) {
        eliminarDelCarrito(productoId);
        return;
    }

    if (nuevaCantidad > item.stock) {
        showError(`Stock insuficiente. Disponible: ${item.stock}`);
        return;
    }

    item.cantidad = nuevaCantidad;
    item.subtotal = item.precio * item.cantidad;
    actualizarCarrito();
}

function eliminarDelCarrito(productoId) {
    carrito = carrito.filter(item => item.id !== productoId);
    actualizarCarrito();
}

function actualizarCarrito() {
    const cartItemsContainer = $('#cart-items');
    const emptyCart = $('#empty-cart');
    const totalesSection = $('#totales-section');
    const btnProcesar = $('#btn-procesar-venta');
    const btnLimpiar = $('#btn-limpiar-carrito');
    const cartCount = $('#cart-count');

    if (carrito.length === 0) {
        emptyCart.show();
        totalesSection.hide();
        btnProcesar.prop('disabled', true);
        btnLimpiar.prop('disabled', true);
        cartCount.text('0');
        return;
    }

    emptyCart.hide();
    totalesSection.show();
    btnProcesar.prop('disabled', false);
    btnLimpiar.prop('disabled', false);

    let html = '';
    let subtotal = 0;

    carrito.forEach(item => {
        subtotal += item.subtotal;
        html += `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">${item.nombre}</h6>
                        <small class="text-muted">$${formatNumber(item.precio)} c/u</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${item.id})" title="Eliminar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="quantity-controls">
                        <button type="button" onclick="cambiarCantidad(${item.id}, ${item.cantidad - 1})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="form-control form-control-sm text-center" 
                               style="width: 60px;" value="${item.cantidad}" min="1" max="${item.stock}"
                               onchange="cambiarCantidad(${item.id}, parseInt(this.value))">
                        <button type="button" onclick="cambiarCantidad(${item.id}, ${item.cantidad + 1})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <strong>$${formatNumber(item.subtotal)}</strong>
                </div>
            </div>
        `;
    });

    cartItemsContainer.html(html);

    // Calcular totales
    const impuesto = subtotal * 0.19;
    const total = subtotal + impuesto;

    $('#subtotal').text(formatCurrency(subtotal));
    $('#impuesto').text(formatCurrency(impuesto));
    $('#total').text(formatCurrency(total));
    cartCount.text(carrito.length);
}

function procesarVenta() {
    if (carrito.length === 0) {
        showError('El carrito está vacío');
        return;
    }

    const tipoCliente = $('input[name="tipo_cliente"]:checked').val();
    const clienteId = $('#cliente_id').val();
    const observaciones = $('#observaciones').val();

    // Validaciones
    if (tipoCliente === 'factura' && !clienteId) {
        showError('Debe seleccionar un cliente para facturación');
        return;
    }

    const btnProcesar = $('#btn-procesar-venta');
    setLoadingButton(btnProcesar, true);

    const data = {
        tipo_cliente: tipoCliente,
        cliente_id: clienteId || null,
        productos: carrito.map(item => ({
            id: item.id,
            cantidad: item.cantidad
        })),
        observaciones: observaciones
    };

    $.ajax({
        url: '{{ route("pos.procesar-venta") }}',
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                // Mostrar mensaje de éxito con detalles de la venta
                let mensaje = `Venta procesada exitosamente!\n\n`;
                if (response.venta.numero_factura) {
                    mensaje += `Factura: ${response.venta.numero_factura}\n`;
                }
                mensaje += `Cliente: ${response.venta.cliente}\n`;
                mensaje += `Total: ${formatCurrency(response.venta.total)}\n`;
                mensaje += `Fecha: ${response.venta.fecha}`;

                Swal.fire({
                    icon: 'success',
                    title: '¡Venta Exitosa!',
                    text: mensaje,
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#27ae60'
                }).then(() => {
                    limpiarCarrito();
                    resetearFormulario();
                });

                // Actualizar stock en la interfaz
                carrito.forEach(item => {
                    let producto = productos.find(p => p.id === item.id);
                    if (producto) {
                        producto.stock -= item.cantidad;
                        actualizarStockProducto(item.id, producto.stock);
                    }
                });
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                handleValidationErrors(errors);
                let errorMessage = 'Errores de validación:\n';
                Object.values(errors).forEach(errorArray => {
                    errorMessage += '• ' + errorArray[0] + '\n';
                });
                showError(errorMessage);
            } else {
                const message = xhr.responseJSON?.message || 'Error al procesar la venta';
                showError(message);
            }
        },
        complete: function() {
            setLoadingButton(btnProcesar, false);
        }
    });
}

function limpiarCarrito() {
    carrito = [];
    actualizarCarrito();
}

function resetearFormulario() {
    $('#tipo_mostrador').prop('checked', true);
    $('#cliente-section').hide();
    $('#cliente_id').val('');
    $('#observaciones').val('');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

function actualizarStockProducto(productoId, nuevoStock) {
    const productCard = $(`.pos-product-card[data-producto-id="${productoId}"]`);
    productCard.find('.badge').text(nuevoStock);
    productCard.attr('data-stock', nuevoStock);
    
    if (nuevoStock <= 0) {
        productCard.addClass('opacity-50');
        productCard.find('button').prop('disabled', true).text('Sin Stock');
    }
}
</script>
@endpush