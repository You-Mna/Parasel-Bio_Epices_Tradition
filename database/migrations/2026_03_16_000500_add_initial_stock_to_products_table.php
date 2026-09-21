<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('initial_stock')->nullable()->after('stock');
        });

        // Initialiser initial_stock avec le stock actuel pour les produits existants
        DB::table('products')->update([
            'initial_stock' => DB::raw('stock'),
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('initial_stock');
        });
    }
};

