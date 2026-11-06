<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return Venta::with('cliente')->whereBetween('created_at', [$this->start, $this->end])->get();
    }

    public function map($venta): array
    {
        return [
            $venta->id,
            $venta->cliente->name ?? 'Consumidor Final',
            $venta->cliente->email ?? '—',
            $venta->canal->etiqueta(),
            $venta->metodo_pago->etiqueta(),
            $venta->estado->etiqueta(),
            $venta->total,
            $venta->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID Venta',
            'Cliente',
            'Email',
            'Tipo',
            'Método de Pago',
            'Estado',
            'Total ($)',
            'Fecha',
        ];
    }
}
