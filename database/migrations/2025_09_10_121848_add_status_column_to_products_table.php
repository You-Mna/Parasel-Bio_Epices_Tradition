<?php

use Illuminate\Database\Migrations\Migration;

// Migration volontairement neutralisée car une autre migration ajoute déjà la colonne `status`.
// On garde ce fichier pour conserver l'historique, mais il ne fait aucune modification.

return new class extends Migration
{
    public function up(): void
    {
        // noop (colonne `status` déjà gérée par 2025_01_15_000000_add_status_to_products_table)
    }

    public function down(): void
    {
        // noop
    }
};
