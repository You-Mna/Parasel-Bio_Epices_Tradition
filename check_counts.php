<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();

use App\\Models\\Product;
use App\\Models\\CartItem;

$products = Product::count();
$cartItems = CartItem::count();

echo "Produits: {$products}\n";
echo "Items panier: {$cartItems}\n";

if ($products == 0) {
    echo "L'espace produits est vide.\n";
}
if ($cartItems == 0) {
    echo "L'espace panier est vide.\n";
}



