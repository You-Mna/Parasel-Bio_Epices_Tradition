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
        Schema::table('cart_items', function (Blueprint $table) {
            // Supprimer l'index unique sur cart_key seul
            $table->dropUnique(['cart_key']);
            
            // Créer un index unique composite sur user_id et cart_key
            $table->unique(['user_id', 'cart_key'], 'cart_items_user_cart_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Supprimer l'index unique composite
            $table->dropUnique('cart_items_user_cart_key_unique');
            
            // Remettre l'index unique sur cart_key seul
            $table->unique('cart_key');
        });
    }
};
