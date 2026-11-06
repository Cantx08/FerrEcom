<?php

namespace App\Enums;

enum MetodoEntrega: string
{
    case RetiroEnCaja    = 'retiro_en_caja';
    case EnvioDomicilio  = 'envio_domicilio';

    public function etiqueta(): string
    {
        return match($this) {
            self::RetiroEnCaja   => 'Retiro en caja / tienda',
            self::EnvioDomicilio => 'Envío a domicilio',
        };
    }
}
