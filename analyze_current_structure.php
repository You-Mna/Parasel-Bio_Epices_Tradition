<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANALYSE DE LA STRUCTURE ACTUELLE ===\n\n";

// Analyser les produits Parasel-Bio Marinade
$marinadeProducts = App\Models\Product::where('name', 'LIKE', 'Parasel-Bio Marinade%')->get();

echo "Produits Parasel-Bio Marinade actuels:\n";
foreach ($marinadeProducts as $product) {
    echo "ID: " . $product->id . "\n";
    echo "Nom: " . $product->name . "\n";
    echo "Slug: " . $product->slug . "\n";
    echo "Prix: " . number_format($product->price, 0, ',', ' ') . " FCFA\n";
    echo "Stock: " . $product->stock . "\n";
    echo "Image: " . $product->image . "\n";
    echo "Description: " . substr($product->description, 0, 100) . "...\n";
    echo "---\n";
}

// Vérifier les commandes associées
echo "\nCommandes associées:\n";
foreach ($marinadeProducts as $product) {
    $orderItems = App\Models\OrderItem::where('product_id', $product->id)->count();
    echo "Produit #" . $product->id . ": " . $orderItems . " articles de commande\n";
}

// Vérifier les éléments de panier
echo "\nÉléments de panier associés:\n";
foreach ($marinadeProducts as $product) {
    $cartItems = App\Models\CartItem::where('product_id', $product->id)->count();
    echo "Produit #" . $product->id . ": " . $cartItems . " éléments de panier\n";
}

echo "\n=== PLAN DE REFACTORISATION ===\n";
echo "1. Créer table product_variants\n";
echo "2. Créer produit parent 'Parasel-Bio Marinade'\n";
echo "3. Migrer les 3 produits comme variantes\n";
echo "4. Mettre à jour les modèles et relations\n";
echo "5. Mettre à jour les vues et contrôleurs\n";
echo "6. Créer les redirections 301\n";
echo "7. Supprimer les anciens produits\n";

