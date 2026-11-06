<?php

namespace App\Http\Requests;

use App\Enums\MetodoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePosVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crearPOS', \App\Models\Venta::class);
    }

    public function rules(): array
    {
        return [
            'metodo_pago'          => ['required', Rule::in([MetodoPago::Efectivo->value, MetodoPago::Transferencia->value])],
            'cliente_nombre'       => 'nullable|string|max:100',
            'cliente_documento'    => 'nullable|string|max:20',
            'cliente_direccion'    => 'nullable|string|max:255',
            'cliente_telefono'     => 'nullable|string|max:50',
            'cliente_correo'       => 'nullable|email|max:150',
            'productos'            => 'required|array|min:1',
            'productos.*.id'       => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in'       => 'El canal POS solo acepta efectivo o transferencia bancaria.',
            'productos.required'   => 'Debe agregar al menos un producto.',
        ];
    }
}
