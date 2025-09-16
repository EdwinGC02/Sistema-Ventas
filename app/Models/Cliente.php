<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'documento',
        'email',
        'telefono',
        'direccion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    protected static function booted()
    {
        // Scope global para ordenar siempre por id ascendente
        static::addGlobalScope('ordenPorId', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getTotalComprasAttribute()
    {
        return $this->ventas()->sum('total');
    }

    public function getNumeroComprasAttribute()
    {
        return $this->ventas()->count();
    }
}
