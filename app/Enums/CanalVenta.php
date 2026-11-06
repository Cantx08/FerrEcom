<?php

namespace App\Enums;

enum CanalVenta: string
{
    case POS    = 'pos';
    case Online = 'online';

    public function etiqueta(): string
    {
        return match($this) {
            self::POS    => 'Punto de Venta (POS)',
            self::Online => 'Tienda Online',
        };
    }
}
