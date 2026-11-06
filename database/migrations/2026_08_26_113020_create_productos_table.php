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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida', 30)->default('UNIDAD');
            $table->decimal('precio_venta', 12, 2);
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->decimal('stock_actual', 12, 2)->default(0);
            $table->decimal('stock_minimo', 12, 2)->default(5);
            $table->string('imagen', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->foreignId('categoria_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE productos
            ADD CONSTRAINT chk_productos_precio_venta CHECK (precio_venta >= 0),
            ADD CONSTRAINT chk_productos_precio_compra CHECK (precio_compra >= 0),
            ADD CONSTRAINT chk_productos_stock_actual CHECK (stock_actual >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
