<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\VentaEstado;
use App\Enums\CanalVenta;
use App\Enums\MetodoPago;
use App\Enums\MetodoEntrega;
use Illuminate\Support\Str;

class Venta extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'estado'          => VentaEstado::class,
            'canal'           => CanalVenta::class,
            'metodo_pago'     => MetodoPago::class,
            'metodo_entrega'  => MetodoEntrega::class,
        ];
    }

    // ─── Relaciones ─────────────────────────────────────────────────────────────

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function historial()
    {
        return $this->hasMany(VentaEstadoHistorial::class)->orderBy('created_at', 'desc');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function esOnline(): bool
    {
        return $this->canal === CanalVenta::Online;
    }

    public function esPOS(): bool
    {
        return $this->canal === CanalVenta::POS;
    }

    public function puedeTransicionarA(VentaEstado $nuevoEstado): bool
    {
        return $this->estado->puedeTransicionarA($nuevoEstado);
    }

    /**
     * Genera un código de rastreo único alfanumérico de 10 caracteres con prefijo ONL.
     */
    public static function generarTrackingCode(): string
    {
        do {
            $codigo = 'ONL-' . strtoupper(Str::random(8));
        } while (self::where('tracking_code', $codigo)->exists());

        return $codigo;
    }
}
