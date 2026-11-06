<?php

namespace App\Enums;

enum VentaEstado: string
{
    // Canal POS & Online
    case Entregado          = 'entregado';

    // Canal Online
    case Pagado             = 'pagado';
    case Enviado            = 'enviado';
    case ListoParaRetirar   = 'listo_para_retirar';
    // Ambos canales
    case Cancelado          = 'cancelado';

    public function etiqueta(): string
    {
        return match($this) {
            self::Pagado            => 'Pagado',
            self::Enviado           => 'Enviado',
            self::ListoParaRetirar  => 'Listo para retirar',
            self::Entregado         => 'Entregado',
            self::Cancelado         => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pagado            => 'info',
            self::Enviado           => 'info',
            self::ListoParaRetirar  => 'warning',
            self::Entregado         => 'success',
            self::Cancelado         => 'danger',
        };
    }

    public function transicionesPermitidas(): array
    {
        return match($this) {
            self::Pagado            => [self::Enviado, self::ListoParaRetirar, self::Entregado, self::Cancelado],
            self::Enviado           => [self::Entregado],
            self::ListoParaRetirar  => [self::Entregado],
            default                 => [],
        };
    }

    public function puedeTransicionarA(self $nuevoEstado): bool
    {
        return in_array($nuevoEstado, $this->transicionesPermitidas(), strict: true);
    }

    /** Estados que deben notificar al cliente */
    public static function estadosConNotificacion(): array
    {
        return [
            self::Pagado,
            self::Enviado,
            self::ListoParaRetirar,
            self::Entregado,
        ];
    }
}
