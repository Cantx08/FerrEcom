<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetallePedidoCompra extends Model
{
    use HasFactory;

    
    protected $guarded = [];

    public function productoCatalogo()
    {
        return $this->belongsTo(ProductoProveedor::class, 'producto_catalogo_id');
    }
}
