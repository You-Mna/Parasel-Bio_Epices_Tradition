<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Colonnes de la table products:\n";
$columns = Schema::getColumnListing('products');
foreach ($columns as $column) {
    echo "- $column\n";
}

echo "\nVérification du champ status:\n";
if (in_array('status', $columns)) {
    echo "✓ Le champ 'status' existe\n";
} else {
    echo "✗ Le champ 'status' n'existe pas\n";
}
