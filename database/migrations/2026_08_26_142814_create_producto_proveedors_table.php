<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('producto_proveedors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained()->onDelete('cascade');
            $table->string('sku', 50);
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('stock_disponible', 12, 2)->default(0);
            $table->string('imagen', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->unique(['proveedor_id', 'sku'], 'uk_supplier_sku');
        });

        DB::statement('ALTER TABLE producto_proveedors
            ADD CONSTRAINT chk_producto_proveedors_costo_unitario CHECK (costo_unitario >= 0),
            ADD CONSTRAINT chk_producto_proveedors_stock_disponible CHECK (stock_disponible >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_proveedors');
    }
};
