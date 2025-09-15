@extends('layouts.app')

@section('content')

<!-- Page Produits - Grille de cartes -->
<div class="products-page">
    <div class="page-header">
        <h1 class="page-title">Produits</h1>
        
        @if(session('success'))
            <div class="toast success">
                <span class="toast-icon">✓</span>
                <div class="toast-body">
                    <div class="toast-title">Produit ajouté au panier</div>
                    <a href="{{ route('cart.index') }}" class="toast-link">Voir le panier</a>
                </div>
            </div>
        @endif
    </div>

    <div class="products-container">
        <div class="grid grid-3">
            @foreach($products as $product)
                <div class="product-card">
                    <div class="product-media">
                        @if($product->name === 'Parasel-Bio Marinade')
                            <!-- Image dynamique pour Parasel-Bio Marinade -->
                            <img id="product-image-{{ $product->id }}" 
                                 src="{{ asset('images/parasel115g.jpg') }}" 
                                 alt="{{ $product->name }}"
                                 data-115g="{{ asset('images/parasel115g.jpg') }}"
                                 data-275g="{{ asset('images/parasel275g.jpg') }}"
                                 data-850g="{{ asset('images/parasel850g.jpg') }}">
                        @elseif($product->image)
                            <img src="{{ Str::startsWith($product->image, ['http://','https://']) ? $product->image : asset('images/' . $product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="placeholder"></div>
                        @endif
                    </div>

                    <div class="product-body">
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <p class="product-desc">{{ Str::limit($product->description, 80) }}</p>

                        <div class="product-meta">
                            @if($product->variants && count($product->variants) > 0)
                                <div class="product-variants">
                                    <div class="variant-row">
                                        <label class="variant-label">Poids :</label>
                                        <div class="variant-buttons" data-product-id="{{ $product->id }}">
                                            @foreach($product->variants as $index => $variant)
                                                <button type="button" 
                                                        class="variant-btn {{ $index === 0 ? 'active' : '' }}" 
                                                        data-size="{{ $variant['size'] }}" 
                                                        data-price="{{ $variant['price'] }}"
                                                        title="Prix: {{ $variant['price'] }} FCFA">
                                                    {{ $variant['size'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="variant-meta">
                                    <span class="price" id="price-display-{{ $product->id }}">{{ number_format($product->variants[0]['price'], 0, ',', ' ') }} FCFA</span>
                                    @if($product->stock > 0)
                                        <span class="badge success">En stock</span>
                                    @else
                                        <span class="badge danger">En rupture</span>
                                    @endif
                                </div>
                                <input type="hidden" name="variant_price" id="variant-price-{{ $product->id }}" value="{{ $product->variants[0]['price'] }}">
                            @else
                                @if($product->name === 'Xladjê du Chef Paludier' || $product->name === 'Arôme Parasel' || $product->name === 'ParaStress')
                                    <div class="product-weight-info">
                                        <div class="weight-row">
                                            <label class="weight-label">Poids :</label>
                                            <span class="weight-value">
                                                @if($product->name === 'Xladjê du Chef Paludier')
                                                    850g
                                                @elseif($product->name === 'Arôme Parasel')
                                                    33cl
                                                @elseif($product->name === 'ParaStress')
                                                    850g
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                <div class="product-meta-content">
                                    @if($product->price > 0)
                                        <span class="price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                    @else
                                        <span class="price waiting">Tarif en attente</span>
                                    @endif
                                    @if($product->stock > 0)
                                        <span class="badge success">En stock</span>
                                    @else
                                        <span class="badge danger">En rupture</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="add-to-cart {{ $product->name === 'Xladjê du Chef Paludier' ? 'xladje' : '' }}">
                            @auth
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="product-form">
                                    @csrf
                                    @if($product->variants && count($product->variants) > 0)
                                        <input type="hidden" name="variant_price" id="variant-price-{{ $product->id }}" value="{{ $product->variants[0]['price'] }}" data-default-price="{{ $product->variants[0]['price'] }}">
                                        <input type="hidden" name="variant_size" id="variant-size-{{ $product->id }}" value="{{ $product->variants[0]['size'] }}" data-default-size="{{ $product->variants[0]['size'] }}">
                                    @endif
                                    
                                    <div class="quantity-selector">
                                        <label for="quantity-{{ $product->id }}" class="quantity-label">Quantité :</label>
                                        <div class="quantity-controls">
                                            <button type="button" class="qty-btn qty-minus" onclick="decreaseQuantity({{ $product->id }})">-</button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   id="quantity-{{ $product->id }}" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $product->stock }}" 
                                                   class="qty-input">
                                            <button type="button" class="qty-btn qty-plus" onclick="increaseQuantity({{ $product->id }})">+</button>
                                        </div>
                                    </div>
                                    
                                    @if($product->stock > 0)
                                        <button type="submit" class="btn block">Ajouter au panier</button>
                                    @else
                                        <button type="button" class="btn block" disabled>Ajouter au panier</button>
                                    @endif
                                </form>
                            @else
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="product-form">
                                    @csrf
                                    @if($product->variants && count($product->variants) > 0)
                                        <input type="hidden" name="variant_price" id="variant-price-{{ $product->id }}" value="{{ $product->variants[0]['price'] }}" data-default-price="{{ $product->variants[0]['price'] }}">
                                        <input type="hidden" name="variant_size" id="variant-size-{{ $product->id }}" value="{{ $product->variants[0]['size'] }}" data-default-size="{{ $product->variants[0]['size'] }}">
                                    @endif
                                    
                                    <div class="quantity-selector">
                                        <label for="quantity-{{ $product->id }}" class="quantity-label">Quantité :</label>
                                        <div class="quantity-controls">
                                            <button type="button" class="qty-btn qty-minus" onclick="decreaseQuantity({{ $product->id }})">-</button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   id="quantity-{{ $product->id }}" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $product->stock }}" 
                                                   class="qty-input">
                                            <button type="button" class="qty-btn qty-plus" onclick="increaseQuantity({{ $product->id }})">+</button>
                                        </div>
                                    </div>
                                    
                                    @if($product->stock > 0)
                                        <button type="submit" class="btn block">Ajouter au panier</button>
                                    @else
                                        <button type="button" class="btn block" disabled>Ajouter au panier</button>
                                    @endif
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($products->hasPages())
        <div class="pagination-container">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Fonction globale pour mettre à jour les variantes
function updateVariant(productId, size, price) {
    console.log(`=== MISE À JOUR VARIANTE ===`);
    console.log(`Produit: ${productId}, Taille: ${size}, Prix: ${price}`);
    
    // Mettre à jour le champ caché du prix
    const hiddenPriceField = document.getElementById(`variant-price-${productId}`);
    if (hiddenPriceField) {
        hiddenPriceField.value = price;
        console.log(`✅ Prix mis à jour: ${hiddenPriceField.value}`);
    } else {
        console.error(`❌ Champ prix non trouvé: variant-price-${productId}`);
    }
    
    // Mettre à jour le champ caché de la taille
    const hiddenSizeField = document.getElementById(`variant-size-${productId}`);
    if (hiddenSizeField) {
        hiddenSizeField.value = size;
        console.log(`✅ Taille mise à jour: ${hiddenSizeField.value}`);
    } else {
        console.error(`❌ Champ taille non trouvé: variant-size-${productId}`);
    }
    
    // Mettre à jour l'affichage du prix
    const priceDisplay = document.getElementById(`price-display-${productId}`);
    if (priceDisplay) {
        priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
        console.log(`✅ Affichage prix mis à jour: ${priceDisplay.textContent}`);
    }
    
    // Mettre à jour l'image du produit
    const productImage = document.getElementById(`product-image-${productId}`);
    if (productImage) {
        const newImageSrc = productImage.getAttribute(`data-${size}`);
        if (newImageSrc) {
            productImage.src = newImageSrc;
            console.log(`✅ Image mise à jour: ${newImageSrc}`);
        } else {
            console.error(`❌ Image non trouvée pour la taille: ${size}`);
        }
    } else {
        console.error(`❌ Image produit non trouvée: product-image-${productId}`);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INITIALISATION JAVASCRIPT ===');
    
    // Attacher les événements aux boutons de variantes
    const variantButtons = document.querySelectorAll('.variant-btn');
    console.log(`Trouvé ${variantButtons.length} boutons de variantes`);
    
    variantButtons.forEach((button, index) => {
        console.log(`Bouton ${index}:`, {
            size: button.dataset.size,
            price: button.dataset.price,
            productId: button.closest('.variant-buttons').dataset.productId
        });
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.closest('.variant-buttons').dataset.productId;
            const selectedSize = this.dataset.size;
            const selectedPrice = this.dataset.price;
            
            console.log(`🖱️ CLIC sur bouton - Produit: ${productId}, Taille: ${selectedSize}, Prix: ${selectedPrice}`);
            
            // Désactiver tous les boutons du même groupe
            const allButtons = this.closest('.variant-buttons').querySelectorAll('.variant-btn');
            allButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Mettre à jour les champs
            updateVariant(productId, selectedSize, selectedPrice);
        });
    });
});

// Fonctions pour gérer la quantité
function increaseQuantity(productId) {
    const input = document.getElementById(`quantity-${productId}`);
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQuantity(productId) {
    const input = document.getElementById(`quantity-${productId}`);
    const min = parseInt(input.getAttribute('min'));
    const current = parseInt(input.value);
    if (current > min) {
        input.value = current - 1;
    }
}
</script>
@endpush

