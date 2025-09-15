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
                        @if($product->has_variants)
                            <!-- Image dynamique pour les produits avec variantes -->
                            @php $firstVariant = $product->variants()->first(); @endphp
                            <img id="product-image-{{ $product->id }}" 
                                 src="{{ asset('images/' . ($firstVariant->image ?? $product->image)) }}" 
                                 alt="{{ $product->name }}">
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
                            @if($product->has_variants)
                                <!-- Produit avec variantes -->
                                <div class="product-variants">
                                    <div class="variant-row">
                                        <label class="variant-label">Poids :</label>
                                        <div class="variant-buttons" data-product-id="{{ $product->id }}">
                                            @foreach($product->variants() as $variant)
                                                <button type="button" 
                                                        class="variant-btn {{ $loop->first ? 'active' : '' }}" 
                                                        data-variant-id="{{ $variant->id }}"
                                                        data-size="{{ $variant->size }}"
                                                        data-price="{{ $variant->price }}"
                                                        data-image="{{ $variant->image }}"
                                                        data-stock="{{ $variant->stock }}">
                                                    {{ $variant->size }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="variant-meta">
                                        <span class="variant-price" id="price-{{ $product->id }}">
                                            {{ number_format($product->variants()->first()->price, 0, ',', ' ') }} FCFA
                                        </span>
                                        <span class="variant-stock" id="stock-{{ $product->id }}">
                                            Stock: {{ $product->variants()->first()->stock }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <!-- Produit sans variantes -->
                                <div class="product-weight-info">
                                    <span class="weight-label">Prix :</span>
                                    <span class="weight-value">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                </div>
                                
                                <div class="product-weight-info">
                                    <span class="weight-label">Stock :</span>
                                    <span class="weight-value">{{ $product->stock }}</span>
                                </div>
                            @endif

                            <!-- Sélecteur de quantité -->
                            <div class="quantity-selector">
                                <label class="quantity-label">Quantité :</label>
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn minus" data-product-id="{{ $product->id }}">-</button>
                                    <input type="number" 
                                           class="quantity-input" 
                                           id="quantity-{{ $product->id }}" 
                                           value="1" 
                                           min="1" 
                                           data-product-id="{{ $product->id }}">
                                    <button type="button" class="quantity-btn plus" data-product-id="{{ $product->id }}">+</button>
                                </div>
                            </div>

                            <!-- Bouton d'ajout au panier -->
                            @auth
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    @if($product->has_variants)
                                        <input type="hidden" name="variant_id" id="variant-id-{{ $product->id }}" value="{{ $product->variants()->first()->id }}">
                                    @endif
                                    <input type="hidden" name="quantity" id="form-quantity-{{ $product->id }}" value="1">
                                    
                                    <button type="submit" class="add-to-cart" data-product-id="{{ $product->id }}">
                                        <i class="fa-solid fa-cart-plus"></i>
                                        Ajouter au panier
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login.show') }}" class="add-to-cart">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    Ajouter au panier
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des variantes
    document.querySelectorAll('.variant-buttons').forEach(function(buttonGroup) {
        const productId = buttonGroup.getAttribute('data-product-id');
        const buttons = buttonGroup.querySelectorAll('.variant-btn');
        
        buttons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons
                buttons.forEach(btn => btn.classList.remove('active'));
                
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                // Mettre à jour les informations du produit
                updateProductInfo(productId, this);
            });
        });
    });
    
    // Gestion des quantités
    document.querySelectorAll('.quantity-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const input = document.getElementById('quantity-' + productId);
            const currentValue = parseInt(input.value);
            
            if (this.classList.contains('minus') && currentValue > 1) {
                input.value = currentValue - 1;
            } else if (this.classList.contains('plus')) {
                input.value = currentValue + 1;
            }
            
            // Mettre à jour le champ caché du formulaire
            document.getElementById('form-quantity-' + productId).value = input.value;
        });
    });
    
    // Gestion des inputs de quantité
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            document.getElementById('form-quantity-' + productId).value = this.value;
        });
    });
});

function updateProductInfo(productId, variantButton) {
    const variantId = variantButton.getAttribute('data-variant-id');
    const size = variantButton.getAttribute('data-size');
    const price = variantButton.getAttribute('data-price');
    const image = variantButton.getAttribute('data-image');
    const stock = variantButton.getAttribute('data-stock');
    
    // Mettre à jour l'image
    const productImage = document.getElementById('product-image-' + productId);
    if (productImage && image) {
        productImage.src = '/images/' + image;
    }
    
    // Mettre à jour le prix
    const priceElement = document.getElementById('price-' + productId);
    if (priceElement) {
        priceElement.textContent = new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
    }
    
    // Mettre à jour le stock
    const stockElement = document.getElementById('stock-' + productId);
    if (stockElement) {
        stockElement.textContent = 'Stock: ' + stock;
    }
    
    // Mettre à jour le champ caché du formulaire
    const variantIdInput = document.getElementById('variant-id-' + productId);
    if (variantIdInput) {
        variantIdInput.value = variantId;
    }
}
</script>
@endpush
