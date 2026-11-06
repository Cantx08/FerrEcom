<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    
    protected $guarded = [];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
