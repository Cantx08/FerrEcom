<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\PedidoEstado;
use App\Enums\MetodoPago;

class PedidoCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'bodeguero_id',
        'aprobado_por',
        'cancelado_por',
        'motivo_cancelacion',
        'total',
        'estado',
        'metodo_pago',
        'observaciones_proveedor',
    ];

    protected function casts(): array
    {
        return [
            'estado'      => PedidoEstado::class,
            'metodo_pago' => MetodoPago::class,
        ];
    }

    // ─── Relaciones ─────────────────────────────────────────────────────────────

    public function bodeguero()
    {
        return $this->belongsTo(User::class, 'bodeguero_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function canceladoPor()
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedidoCompra::class);
    }

    // ─── Helpers de máquina de estados ──────────────────────────────────────────

    public function puedeTransicionarA(PedidoEstado $nuevoEstado): bool
    {
        return $this->estado->puedeTransicionarA($nuevoEstado);
    }

    public function estaCancelado(): bool
    {
        return $this->estado === PedidoEstado::Cancelado;
    }

    public function esCancelable(): bool
    {
        return in_array($this->estado, PedidoEstado::estadosCancelables(), strict: true);
    }

    public function estaFinalizado(): bool
    {
        return in_array($this->estado, [PedidoEstado::Pagado, PedidoEstado::Cancelado], strict: true);
    }
}
