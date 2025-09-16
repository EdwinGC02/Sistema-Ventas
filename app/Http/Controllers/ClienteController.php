<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:2',
            'documento' => 'required|string|max:20|unique:clientes,documento',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es requerido',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'documento.required' => 'El documento es requerido',
            'documento.unique' => 'Ya existe un cliente con este documento',
            'documento.max' => 'El documento no puede exceder 20 caracteres',
            'email.email' => 'El email debe tener un formato válido',
            'email.max' => 'El email no puede exceder 255 caracteres',
            'telefono.max' => 'El teléfono no puede exceder 15 caracteres',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cliente = Cliente::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'documento' => $cliente->documento,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente'
            ], 500);
        }
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:2',
            'documento' => 'required|string|max:20|unique:clientes,documento,' . $cliente->id,
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es requerido',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'documento.required' => 'El documento es requerido',
            'documento.unique' => 'Ya existe un cliente con este documento',
            'documento.max' => 'El documento no puede exceder 20 caracteres',
            'email.email' => 'El email debe tener un formato válido',
            'email.max' => 'El email no puede exceder 255 caracteres',
            'telefono.max' => 'El teléfono no puede exceder 15 caracteres',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cliente->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'documento' => $cliente->documento,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente'
            ], 500);
        }
    }

    public function toggleActivo(Cliente $cliente)
    {
        try {
            $cliente->update([
                'activo' => !$cliente->activo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del cliente actualizado',
                'activo' => $cliente->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del cliente'
            ], 500);
        }
    }
}