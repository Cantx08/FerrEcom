<?php

namespace App\Enums;

enum MetodoPago: string
{
    case Efectivo      = 'efectivo';
    case Transferencia = 'transferencia';
    case Tarjeta       = 'tarjeta';

    public function etiqueta(): string
    {
        return match($this) {
            self::Efectivo      => 'Efectivo',
            self::Transferencia => 'Transferencia bancaria',
            self::Tarjeta       => 'Tarjeta de crédito/débito',
        };
    }

    /** Métodos permitidos para el canal POS */
    public static function permitidosPOS(): array
    {
        return [self::Efectivo, self::Transferencia];
    }

    /** Métodos permitidos para el canal Online */
    public static function permitidosOnline(): array
    {
        return [self::Tarjeta];
    }

    /** Métodos permitidos para pago de pedido de compra a proveedor */
    public static function permitidosPedidoCompra(): array
    {
        return [self::Efectivo];
    }

    /** Verifica si el método es válido para el canal dado */
    public static function esValidoParaCanal(self $metodo, CanalVenta $canal): bool
    {
        $permitidos = match($canal) {
            CanalVenta::POS    => self::permitidosPOS(),
            CanalVenta::Online => self::permitidosOnline(),
        };

        return in_array($metodo, $permitidos, strict: true);
    }
}
