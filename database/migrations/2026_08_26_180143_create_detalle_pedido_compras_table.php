<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_pedido_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_compra_id')->constrained('pedido_compras')->onDelete('cascade');
            $table->foreignId('producto_catalogo_id')->constrained('producto_proveedors')->onDelete('cascade');
            $table->integer('cantidad_solicitada');
            $table->integer('cantidad_disponible')->nullable();
            $table->decimal('precio', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedido_compras');
    }
};
