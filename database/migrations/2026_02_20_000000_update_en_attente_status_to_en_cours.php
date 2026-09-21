<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalise les anciens statuts techniques vers les 3 statuts métier :
        // - "en_cours"
        // - "livree"
        // - "annulee"

        // Ancien statut "en_attente" → "en_cours"
        DB::table('orders')
            ->where('status', 'en_attente')
            ->update(['status' => 'en_cours']);

        // Ancien statut "paid" ou "pending_payment" → "en_cours"
        DB::table('orders')
            ->whereIn('status', ['paid', 'pending_payment'])
            ->update(['status' => 'en_cours']);

        // Ancien statut "payment_failed" → "annulee"
        DB::table('orders')
            ->where('status', 'payment_failed')
            ->update(['status' => 'annulee']);
    }

    public function down(): void
    {
        // Pas de rollback fiable : on ne sait pas si "en_cours" venait de "en_attente"
        // On laisse donc vide intentionnellement.
    }
};

