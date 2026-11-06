<?php

namespace App\Http\Requests;

use App\Enums\PedidoEstado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePedidoEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pedido = $this->route('pedido') ?? \App\Models\PedidoCompra::findOrFail($this->route('id'));
        return $this->user()->can('cambiarEstado', $pedido);
    }

    public function rules(): array
    {
        return [
            'estado'  => ['required', Rule::enum(PedidoEstado::class)],
            'notas'   => 'nullable|string|max:500',
            'motivo_cancelacion' => 'required_if:estado,cancelado|nullable|string|max:500',

            // Para cuando el proveedor revisa stock
            'detalles'           => 'nullable|array',
            'detalles.*.id'      => 'nullable|exists:detalle_pedido_compras,id',
            'detalles.*.cantidad_disponible' => 'nullable|numeric|min:0',
            'observaciones_proveedor'        => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'estado.required'              => 'Debe especificar el nuevo estado.',
            'motivo_cancelacion.required_if' => 'Debe indicar el motivo de la cancelación.',
        ];
    }
}
