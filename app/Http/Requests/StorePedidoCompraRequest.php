<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\PedidoCompra::class);
    }

    public function rules(): array
    {
        return [
            'proveedor_id'                   => 'required|exists:proveedors,id',
            'productos'                       => 'required|array|min:1',
            'productos.*.id'                  => 'required|exists:producto_proveedors,id',
            'productos.*.cantidad'            => 'required|numeric|min:1',
            'productos.*.precio_unitario'     => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'proveedor_id.exists'   => 'El proveedor seleccionado no existe.',
            'productos.required'    => 'Debe agregar al menos un producto al pedido.',
            'productos.min'         => 'Debe agregar al menos un producto al pedido.',
        ];
    }
}
