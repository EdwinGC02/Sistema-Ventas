<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'subtotal',
        'impuesto',
        'total',
        'tipo_cliente',
        'numero_factura',
        'observaciones',
        'fecha_venta'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_venta' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_venta', $fechaInicio);
    }

    public function scopePorTipoCliente($query, $tipo)
    {
        return $query->where('tipo_cliente', $tipo);
    }

    public static function generarNumeroFactura()
    {
        $ultimo = self::where('numero_factura', '!=', null)
                     ->orderBy('id', 'desc')
                     ->first();
        
        if (!$ultimo) {
            return 'F-00001';
        }
        
        $numero = intval(substr($ultimo->numero_factura, 2)) + 1;
        return 'F-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }

    public function getNombreClienteAttribute()
    {
        return $this->tipo_cliente === 'mostrador' ? 'Cliente de Mostrador' : $this->cliente->nombre;
    }
}