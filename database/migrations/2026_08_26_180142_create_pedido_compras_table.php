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
        Schema::create('pedido_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bodeguero_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('proveedor_id')->constrained('proveedors')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'revisado', 'stock_incompleto', 'confirmado', 'enviado', 'completado'])->default('pendiente');
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_compras');
    }
};
