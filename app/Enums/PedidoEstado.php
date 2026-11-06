<?php

namespace App\Enums;

enum PedidoEstado: string
{
    case Borrador           = 'borrador';
    case EnAprobacion       = 'en_aprobacion';
    case Emitido            = 'emitido';
    case Aceptado           = 'aceptado';
    case EnPreparacion      = 'en_preparacion';
    case EnTransito         = 'en_transito';
    case RecibidoParcial    = 'recibido_parcialmente';
    case RecibidoCompleto   = 'recibido_completado';
    case Pagado             = 'pagado';
    case Cancelado          = 'cancelado';

    /** Etiqueta legible para la UI */
    public function etiqueta(): string
    {
        return match($this) {
            self::Borrador          => 'Borrador',
            self::EnAprobacion      => 'En aprobación',
            self::Emitido           => 'Emitido',
            self::Aceptado          => 'Aceptado por proveedor',
            self::EnPreparacion     => 'En preparación',
            self::EnTransito        => 'Enviado',
            self::RecibidoParcial   => 'Recibido parcialmente',
            self::RecibidoCompleto  => 'Recibido completamente',
            self::Pagado            => 'Pagado',
            self::Cancelado         => 'Cancelado',
        };
    }

    /** Color de badge para la UI */
    public function color(): string
    {
        return match($this) {
            self::Borrador          => 'secondary',
            self::EnAprobacion      => 'warning',
            self::Emitido           => 'info',
            self::Aceptado          => 'primary',
            self::EnPreparacion     => 'primary',
            self::EnTransito        => 'info',
            self::RecibidoParcial   => 'warning',
            self::RecibidoCompleto  => 'success',
            self::Pagado            => 'success',
            self::Cancelado         => 'danger',
        };
    }

    /**
     * Define las transiciones válidas para cada estado.
     * Devuelve los estados a los que se puede pasar DESDE el estado actual.
     *
     * @return PedidoEstado[]
     */
    public function transicionesPermitidas(): array
    {
        return match($this) {
            self::Borrador          => [self::EnAprobacion, self::Emitido, self::Cancelado],
            self::EnAprobacion      => [self::Emitido, self::Cancelado],
            self::Emitido           => [self::Aceptado, self::Cancelado],
            self::Aceptado          => [self::EnPreparacion, self::Cancelado],
            self::EnPreparacion     => [self::EnTransito, self::Cancelado],
            self::EnTransito        => [self::RecibidoParcial, self::RecibidoCompleto, self::Cancelado],
            self::RecibidoParcial   => [self::RecibidoCompleto],
            self::RecibidoCompleto  => [self::Pagado],
            self::Pagado            => [],
            self::Cancelado         => [],
        };
    }

    /** Verifica si la transición al estado dado es válida */
    public function puedeTransicionarA(self $nuevoEstado): bool
    {
        return in_array($nuevoEstado, $this->transicionesPermitidas(), strict: true);
    }

    /** Estados que puede cancelar (cualquier punto antes de recibir mercadería o pagar) */
    public static function estadosCancelables(): array
    {
        return [
            self::Borrador,
            self::EnAprobacion,
            self::Emitido,
            self::Aceptado,
            self::EnPreparacion,
            self::EnTransito,
        ];
    }

    /**
     * Estados que representan un compromiso de compra ya formalizado con el
     * proveedor (excluye borrador/en_aprobacion, que aún no se emitieron, y
     * cancelado, que no representa gasto real). Usado para reportes de gasto.
     */
    public static function estadosComprometidos(): array
    {
        return [
            self::Emitido,
            self::Aceptado,
            self::EnPreparacion,
            self::EnTransito,
            self::RecibidoParcial,
            self::RecibidoCompleto,
            self::Pagado,
        ];
    }

    /** @return string[] valores string de estadosComprometidos(), listos para whereIn() */
    public static function valoresComprometidos(): array
    {
        return array_map(fn (self $e) => $e->value, self::estadosComprometidos());
    }
}
