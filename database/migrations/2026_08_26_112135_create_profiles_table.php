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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string(('nombres'));
            $table->string(('apellidos'));
            $table->string(('email'))->unique();
            $table->string(('telefono'))->nullable();
            $table->string(('tipo_identificacion'))->default('CEDULA');
            $table->string(('identificacion'))->unique()->nullable();
            $table->text(('direccion'))->nullable();
            $table->boolean(('estado'))->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
