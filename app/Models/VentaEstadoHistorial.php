<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaEstadoHistorial extends Model
{
    public $timestamps = false;

    protected $table = 'venta_estado_historial';

    protected $fillable = [
        'venta_id',
        'estado_anterior',
        'estado_nuevo',
        'user_id',
        'notas',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
