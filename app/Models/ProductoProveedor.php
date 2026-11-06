<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductoProveedor extends Model
{
    use HasFactory;

    
    protected $guarded = [];
public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
