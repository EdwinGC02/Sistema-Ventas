<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::create([
            'nombre' => 'Edwin Gelvez',
            'documento' => '1005329209',
            'email' => 'edwin@cliente.com',
            'telefono' => '3174806560',
            'direccion' => 'Carrera 10 #4D - 1',
            'activo' => true
        ]);
    }
}