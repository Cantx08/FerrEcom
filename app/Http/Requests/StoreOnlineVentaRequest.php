<?php

namespace App\Http\Requests;

use App\Enums\MetodoEntrega;
use App\Enums\MetodoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOnlineVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crearOnline', \App\Models\Venta::class);
    }

    public function rules(): array
    {
        return [
            'metodo_pago'     => ['required', Rule::in([MetodoPago::Tarjeta->value])],
            'metodo_entrega'  => ['required', Rule::enum(MetodoEntrega::class)],
            'razon_social'    => 'required|string|max:150',
            'ruc_cedula'      => 'required|string|max:20',
            'direccion'       => 'required|string|max:255',
            'telefono'        => 'nullable|string|max:50',
            'correo'          => 'required|email|max:150',
            'cart_data'       => 'required|string', // JSON del carrito
        ];
    }

    public function messages(): array
    {
        return [
            'metodo_pago.in'         => 'Las compras en línea solo aceptan pago con tarjeta.',
            'metodo_entrega.required' => 'Debe seleccionar el método de entrega.',
            'cart_data.required'     => 'El carrito está vacío.',
        ];
    }
}
