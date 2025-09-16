<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function ventaProductos()
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function tieneStock($cantidad = 1)
    {
        return $this->stock >= $cantidad;
    }

    public function reducirStock($cantidad)
    {
        if ($this->tieneStock($cantidad)) {
            $this->decrement('stock', $cantidad);
            return true;
        }
        return false;
    }

    public function aumentarStock($cantidad)
    {
        $this->increment('stock', $cantidad);
    }
}