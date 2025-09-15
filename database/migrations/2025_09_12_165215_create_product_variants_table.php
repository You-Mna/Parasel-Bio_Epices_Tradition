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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size'); // ex: "115g", "275g", "850g"
            $table->decimal('price', 10, 2); // Prix spécifique à cette variante
            $table->string('image')->nullable(); // Image spécifique à cette variante
            $table->integer('stock')->default(0); // Stock spécifique à cette variante
            $table->boolean('is_active')->default(true); // Variante active/inactive
            $table->integer('sort_order')->default(0); // Ordre d'affichage
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
