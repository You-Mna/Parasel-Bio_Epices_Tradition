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
        @if($products->isEmpty())
            <div class="empty-state">
                <p>Aucun produit disponible pour le moment.</p>
            </div>
        @else
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
                                            @foreach($product->variants as $variant)
                                                <button type="button" 
                                                        class="variant-btn {{ $loop->first ? 'active' : '' }}" 
                                                        data-size="{{ $variant['size'] }}" 
                                                        data-price="{{ $variant['price'] }}"
                                                        data-image="{{ $variant['image'] ?? '' }}"
                                                        data-stock="{{ $variant['stock'] }}">
                                                    {{ $variant['size'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="variant-meta" style="display: flex; justify-content: space-between; align-items: center;">
                                        <span class="variant-price" id="price-{{ $product->id }}">
                                            {{ number_format($product->variants[0]['price'], 0, ',', ' ') }} FCFA
                                        </span>
                                        <span class="variant-stock {{ $product->variants[0]['stock'] > 0 ? 'stock-available' : 'stock-unavailable' }}" id="stock-{{ $product->id }}">
                                            {{ $product->variants[0]['stock'] > 0 ? 'EN STOCK' : 'EN RUPTURE' }}
                                        </span>
                                </div>
                                </div>
                            @else
                                <!-- Produit sans variantes -->
                                <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="weight-label">Prix :</span>
                                    <span class="weight-value">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                
                                <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="weight-label">Stock :</span>
                                    <span class="weight-value {{ $product->stock > 0 ? 'stock-available' : 'stock-unavailable' }}">{{ $product->stock > 0 ? 'EN STOCK' : 'EN RUPTURE' }}</span>
                                </div>
                            @endif

                            <!-- Sélecteur de quantité -->
                            <div class="quantity-selector">
                                <label class="quantity-label">Quantité :</label>
                                <div class="quantity-modern">
                                    <button type="button" class="qty-btn minus" data-product-id="{{ $product->id }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                    <span class="qty-display" id="quantity-display-{{ $product->id }}">1</span>
                                    <button type="button" class="qty-btn plus" data-product-id="{{ $product->id }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                </div>
                        </div>

                            <!-- Bouton d'ajout au panier -->
                            @auth
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    @if($product->variants && count($product->variants) > 0)
                                        <input type="hidden" name="variant_price" id="variant-price-{{ $product->id }}" value="{{ $product->variants[0]['price'] }}">
                                        <input type="hidden" name="variant_size" id="variant-size-{{ $product->id }}" value="{{ $product->variants[0]['size'] }}">
                                    @endif
                                    <input type="hidden" name="quantity" id="form-quantity-{{ $product->id }}" value="1">
                                    
                                    @if($product->variants && count($product->variants) > 0)
                                        @if($product->variants[0]['stock'] > 0)
                                            <button type="submit" class="add-to-cart" data-product-id="{{ $product->id }}">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                Ajouter au panier
                                            </button>
                                        @else
                                            <button type="button" class="add-to-cart" disabled data-product-id="{{ $product->id }}">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                Ajouter au panier
                                            </button>
                                        @endif
                                    @else
                                        @if($product->stock > 0)
                                            <button type="submit" class="add-to-cart" data-product-id="{{ $product->id }}">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                Ajouter au panier
                                            </button>
                                        @else
                                            <button type="button" class="add-to-cart" disabled data-product-id="{{ $product->id }}">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                Ajouter au panier
                                            </button>
                                        @endif
                                    @endif
                                </form>
                            @else
                                @if($product->variants && count($product->variants) > 0)
                                    @if($product->variants[0]['stock'] > 0)
                                        <a href="{{ route('login.show') }}" class="add-to-cart">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            Ajouter au panier
                                        </a>
                                    @else
                                        <button type="button" class="add-to-cart" disabled data-product-id="{{ $product->id }}">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            Ajouter au panier
                                        </button>
                                    @endif
                                @else
                                    @if($product->stock > 0)
                                        <a href="{{ route('login.show') }}" class="add-to-cart">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            Ajouter au panier
                                        </a>
                                    @else
                                        <button type="button" class="add-to-cart" disabled data-product-id="{{ $product->id }}">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            Ajouter au panier
                                        </button>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Boutons de variantes
    var variantBtns = document.querySelectorAll('.variant-btn');
    for (var i = 0; i < variantBtns.length; i++) {
        variantBtns[i].onclick = function() {
            // Retirer active de tous les boutons du même groupe
            var parent = this.closest('.variant-buttons');
            var allBtns = parent.querySelectorAll('.variant-btn');
            for (var j = 0; j < allBtns.length; j++) {
                allBtns[j].classList.remove('active');
            }
            
            // Ajouter active au cliqué
            this.classList.add('active');
            
            // Changer image
            var productId = parent.getAttribute('data-product-id');
            var img = document.getElementById('product-image-' + productId);
            var size = this.getAttribute('data-size');
            
            if (img) {
                if (size === '115g') {
                    img.src = '/images/parasel115g.jpg';
                } else if (size === '275g') {
                    img.src = '/images/parasel275g.jpg';
                } else if (size === '850g') {
                    img.src = '/images/parasel850g.jpg';
                }
            }
            
            // Changer prix
            var priceEl = document.getElementById('price-' + productId);
            if (priceEl) {
                var price = this.getAttribute('data-price');
                priceEl.textContent = parseInt(price).toLocaleString('fr-FR') + ' FCFA';
            }
            
            // Changer stock
            var stockEl = document.getElementById('stock-' + productId);
            if (stockEl) {
                var stock = this.getAttribute('data-stock');
                stockEl.textContent = stock > 0 ? 'EN STOCK' : 'EN RUPTURE';
                stockEl.className = 'variant-stock ' + (stock > 0 ? 'stock-available' : 'stock-unavailable');
            }
            
            // Mettre à jour le bouton "Ajouter au panier"
            var addToCartBtn = document.querySelector('.add-to-cart-form [data-product-id="' + productId + '"]');
            if (addToCartBtn) {
                var stock = parseInt(this.getAttribute('data-stock'));
                if (stock > 0) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.type = 'submit';
                } else {
                    addToCartBtn.disabled = true;
                    addToCartBtn.type = 'button';
                }
            }
            
            // Mettre à jour les champs cachés du formulaire
            var variantPriceInput = document.getElementById('variant-price-' + productId);
            var variantSizeInput = document.getElementById('variant-size-' + productId);
            if (variantPriceInput) {
                variantPriceInput.value = this.getAttribute('data-price');
            }
            if (variantSizeInput) {
                variantSizeInput.value = this.getAttribute('data-size');
            }
        };
    }
    
    // Boutons de quantité
    var qtyBtns = document.querySelectorAll('.qty-btn');
    for (var i = 0; i < qtyBtns.length; i++) {
        qtyBtns[i].onclick = function() {
            var productId = this.getAttribute('data-product-id');
            var display = document.getElementById('quantity-display-' + productId);
            var currentValue = parseInt(display.textContent);
            
            if (this.classList.contains('minus') && currentValue > 1) {
                display.textContent = currentValue - 1;
            } else if (this.classList.contains('plus')) {
                display.textContent = currentValue + 1;
            }
            
            // Mettre à jour le champ caché du formulaire
            var formQty = document.getElementById('form-quantity-' + productId);
            if (formQty) {
                formQty.value = display.textContent;
            }
        };
    }
});
</script>
@endpush