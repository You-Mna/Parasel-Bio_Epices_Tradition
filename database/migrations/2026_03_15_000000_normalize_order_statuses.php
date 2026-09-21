<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalise les statuts de commande :
     * - payee → payee_en_ligne (payée en ligne)
     * - livree → livree_payee (livrée payée)
     */
    public function up(): void
    {
        DB::table('orders')->where('status', 'payee')->update(['status' => 'payee_en_ligne']);
        DB::table('orders')->where('status', 'livree')->update(['status' => 'livree_payee']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->where('status', 'payee_en_ligne')->update(['status' => 'payee']);
        DB::table('orders')->where('status', 'livree_payee')->update(['status' => 'livree']);
    }
};
