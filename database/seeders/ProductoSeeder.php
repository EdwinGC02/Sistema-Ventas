<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        Producto::create([
            'nombre' => 'Empanadas',
            'precio' => 2500.00,
            'stock' => 100,
            'activo' => true
        ]);

        Producto::create([
            'nombre' => 'Papas Rellenas',
            'precio' => 3000.00,
            'stock' => 50,
            'activo' => true
        ]);
    }
}