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
                <div class="product-card" id="product-{{ $product->id }}">
                    <div class="product-media">
                        @if($product->name === 'Parasel-Bio Marinade')
                            <!-- Image dynamique pour Parasel-Bio Marinade -->
                            <img id="product-image-{{ $product->id }}" 
                                 src="{{ asset('images/parasel115g.jpg') }}" 
                                 alt="{{ $product->name }}"
                                 class="zoomable"
                                 data-zoom-src="{{ asset('images/parasel115g.jpg') }}"
                                 data-115g="{{ asset('images/parasel115g.jpg') }}"
                                 data-275g="{{ asset('images/parasel275g.jpg') }}"
                                 data-850g="{{ asset('images/parasel850g.jpg') }}">
                        @elseif($product->image)
                            <img src="{{ Str::startsWith($product->image, ['http://','https://']) ? $product->image : asset('images/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="zoomable"
                                 data-zoom-src="{{ Str::startsWith($product->image, ['http://','https://']) ? $product->image : asset('images/' . $product->image) }}">
                        @else
                            <div class="placeholder"></div>
                        @endif

                        <button type="button" class="zoom-hint" aria-label="Zoom sur l'image" tabindex="-1">
                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                        </button>
                    </div>

                    @php
                        $actionUrl = null;
                        foreach (['jpg','png','webp'] as $ext) {
                            $candidate = public_path('images/action/' . $product->id . '.' . $ext);
                            if (file_exists($candidate)) {
                                $actionUrl = asset('images/action/' . $product->id . '.' . $ext);
                                break;
                            }
                        }
                    @endphp
                    @if($actionUrl)
                        <div class="product-action">
                            <div class="product-action-label">Produit en action</div>
                            <img src="{{ $actionUrl }}" alt="{{ $product->name }} en action" class="product-action-img zoomable" data-zoom-src="{{ $actionUrl }}">
                        </div>
                    @endif

                    <div class="product-body">
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <p class="product-desc">{{ Str::limit($product->description, 80) }}</p>

                        <div class="product-meta">
                            @if($product->name === 'Parasel-Bio Marinade' && $product->variants && count($product->variants) > 0)
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
                                            {{ $product->variants[0]['stock'] > 0 ? 'En stock' : 'En rupture' }}
                                        </span>
                                </div>
                                </div>
                            @else
                                <!-- Produit sans variantes -->
                                <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="weight-label">Prix :</span>
                                    <span class="weight-value" id="price-{{ $product->id }}">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                
                                <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="weight-label">Stock :</span>
                                    <span class="weight-value {{ $product->stock > 0 ? 'stock-available' : 'stock-unavailable' }}" id="stock-{{ $product->id }}">{{ $product->stock > 0 ? 'En stock' : 'En rupture' }}</span>
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

                            <div class="product-total">
                                <span class="total-label">Total :</span>
                                <span class="total-value" id="total-{{ $product->id }}">
                                    @if($product->name === 'Parasel-Bio Marinade' && $product->variants && count($product->variants) > 0)
                                        {{ number_format($product->variants[0]['price'], 0, ',', ' ') }} FCFA
                                    @else
                                        {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                    @endif
                                </span>
                            </div>

                            <!-- Bouton d'ajout au panier -->
                            @auth
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    @if($product->name === 'Parasel-Bio Marinade' && $product->variants && count($product->variants) > 0)
                                        <input type="hidden" name="variant_price" id="variant-price-{{ $product->id }}" value="{{ $product->variants[0]['price'] }}">
                                        <input type="hidden" name="variant_size" id="variant-size-{{ $product->id }}" value="{{ $product->variants[0]['size'] }}">
                                    @endif
                                    <input type="hidden" name="quantity" id="form-quantity-{{ $product->id }}" value="1">
                                    
                                    @if($product->name === 'Parasel-Bio Marinade' && $product->variants && count($product->variants) > 0)
                                        @if($product->variants[0]['stock'] > 0)
                                            <button type="submit" class="add-to-cart" data-product-id="{{ $product->id }}" data-loading-text="Ajout en cours...">
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
                                            <button type="submit" class="add-to-cart" data-product-id="{{ $product->id }}" data-loading-text="Ajout en cours...">
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
                                {{-- Invité : formulaire POST vers cart.add → redirection login puis retour sur ce produit --}}
                                @if($product->name === 'Parasel-Bio Marinade' && $product->variants && count($product->variants) > 0)
                                    @if($product->variants[0]['stock'] > 0)
                                        <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="variant_price" id="guest-variant-price-{{ $product->id }}" value="{{ $product->variants[0]['price'] }}">
                                            <input type="hidden" name="variant_size" id="guest-variant-size-{{ $product->id }}" value="{{ $product->variants[0]['size'] }}">
                                            <input type="hidden" name="quantity" id="guest-form-quantity-{{ $product->id }}" value="1">
                                            <button type="submit" class="add-to-cart" data-product-id="{{ $product->id }}" data-loading-text="Ajout en cours...">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                Ajouter au panier
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="add-to-cart" disabled data-product-id="{{ $product->id }}">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            Ajouter au panier
                                        </button>
                                    @endif
                                @else
                                    @if($product->stock > 0)
                                        <form action="{{ route('cart.add', $product) }}" method="POST" class="add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="quantity" id="guest-form-quantity-{{ $product->id }}" value="1">
                                            <button type="submit" class="add-to-cart" data-product-id="{{ $product->id }}">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                Ajouter au panier
                                            </button>
                                        </form>
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

<!-- Modal Zoom Image -->
<div class="zoom-modal" id="zoom-modal" aria-hidden="true">
    <div class="zoom-backdrop" data-zoom-close></div>
    <div class="zoom-dialog" role="dialog" aria-modal="true" aria-label="Aperçu image">
        <button type="button" class="zoom-close" data-zoom-close aria-label="Fermer">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="zoom-stage" id="zoom-stage">
            <img class="zoom-img" id="zoom-img" alt="">
        </div>
        <div class="zoom-help">Astuce : double-clic/tap pour zoomer</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll vers le produit après retour connexion (hash #product-XX)
    var hash = window.location.hash;
    if (hash && hash.indexOf('product-') === 1) {
        var el = document.getElementById(hash.slice(1));
        if (el) {
            setTimeout(function() { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
        }
    }

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
                var stock = parseInt(this.getAttribute('data-stock'), 10);
                stockEl.textContent = stock > 0 ? 'En stock' : 'En rupture';
                stockEl.className = 'variant-stock ' + (stock > 0 ? 'stock-available' : 'stock-unavailable');
            }

            // Mettre à jour total
            updateTotal(productId);
            
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
            
            // Mettre à jour les champs cachés du formulaire (connecté et invité)
            var variantPriceInput = document.getElementById('variant-price-' + productId);
            var variantSizeInput = document.getElementById('variant-size-' + productId);
            var guestPriceInput = document.getElementById('guest-variant-price-' + productId);
            var guestSizeInput = document.getElementById('guest-variant-size-' + productId);
            var price = this.getAttribute('data-price');
            var size = this.getAttribute('data-size');
            if (variantPriceInput) variantPriceInput.value = price;
            if (variantSizeInput) variantSizeInput.value = size;
            if (guestPriceInput) guestPriceInput.value = price;
            if (guestSizeInput) guestSizeInput.value = size;
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
            
            // Mettre à jour le champ caché du formulaire (connecté et invité)
            var formQty = document.getElementById('form-quantity-' + productId);
            var guestFormQty = document.getElementById('guest-form-quantity-' + productId);
            if (formQty) formQty.value = display.textContent;
            if (guestFormQty) guestFormQty.value = display.textContent;

            // Mettre à jour total
            updateTotal(productId);
        };
    }

    function getUnitPrice(productId) {
        var activeVariant = document.querySelector('.variant-buttons[data-product-id="' + productId + '"] .variant-btn.active');
        if (activeVariant) {
            return parseInt(activeVariant.getAttribute('data-price') || '0');
        }
        // produit sans variante: lire le prix affiché
        var priceEl = document.getElementById('price-' + productId);
        if (!priceEl) return 0;
        var txt = priceEl.textContent || '';
        var digits = txt.replace(/[^\d]/g, '');
        return parseInt(digits || '0');
    }

    function getQty(productId) {
        var display = document.getElementById('quantity-display-' + productId);
        return display ? parseInt(display.textContent || '1') : 1;
    }

    function updateTotal(productId) {
        var totalEl = document.getElementById('total-' + productId);
        if (!totalEl) return;
        var total = getUnitPrice(productId) * getQty(productId);
        totalEl.textContent = total.toLocaleString('fr-FR') + ' FCFA';
    }

    // Init totals
    var cards = document.querySelectorAll('.product-card[id^="product-"]');
    for (var k = 0; k < cards.length; k++) {
        var id = cards[k].id.replace('product-', '');
        updateTotal(id);
    }

    // Zoom modal
    var zoomModal = document.getElementById('zoom-modal');
    var zoomImg = document.getElementById('zoom-img');
    var zoomStage = document.getElementById('zoom-stage');
    var zoomScale = 1;
    var panX = 0, panY = 0;
    var dragging = false;
    var startX = 0, startY = 0;

    function applyZoom() {
        if (!zoomImg) return;
        zoomImg.style.transform = 'translate3d(' + panX + 'px,' + panY + 'px,0) scale(' + zoomScale + ')';
        zoomImg.style.cursor = zoomScale > 1 ? (dragging ? 'grabbing' : 'grab') : 'default';
    }

    function openZoom(src, alt) {
        if (!zoomModal || !zoomImg) return;
        zoomScale = 1; panX = 0; panY = 0; dragging = false;
        zoomImg.src = src;
        zoomImg.alt = alt || '';
        applyZoom();
        zoomModal.classList.add('show');
        zoomModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeZoom() {
        if (!zoomModal) return;
        zoomModal.classList.remove('show');
        zoomModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    var zoomables = document.querySelectorAll('.zoomable[data-zoom-src]');
    for (var z = 0; z < zoomables.length; z++) {
        zoomables[z].addEventListener('click', function() {
            openZoom(this.getAttribute('data-zoom-src'), this.getAttribute('alt'));
        });
    }

    var closers = document.querySelectorAll('[data-zoom-close]');
    for (var c = 0; c < closers.length; c++) {
        closers[c].addEventListener('click', closeZoom);
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeZoom();
    });

    if (zoomStage) {
        zoomStage.addEventListener('dblclick', function() {
            zoomScale = zoomScale >= 3 ? 1 : (zoomScale >= 2 ? 3 : 2);
            panX = 0; panY = 0;
            applyZoom();
        });
        // tap/double tap (simple)
        var lastTap = 0;
        zoomStage.addEventListener('touchend', function() {
            var now = Date.now();
            if (now - lastTap < 280) {
                zoomScale = zoomScale >= 3 ? 1 : (zoomScale >= 2 ? 3 : 2);
                panX = 0; panY = 0;
                applyZoom();
            }
            lastTap = now;
        }, { passive: true });

        zoomStage.addEventListener('wheel', function(e) {
            e.preventDefault();
            var delta = e.deltaY > 0 ? -0.12 : 0.12;
            zoomScale = Math.max(1, Math.min(4, zoomScale + delta));
            if (zoomScale === 1) { panX = 0; panY = 0; }
            applyZoom();
        }, { passive: false });

        zoomStage.addEventListener('pointerdown', function(e) {
            if (zoomScale <= 1) return;
            dragging = true;
            startX = e.clientX - panX;
            startY = e.clientY - panY;
            applyZoom();
        });
        window.addEventListener('pointermove', function(e) {
            if (!dragging) return;
            panX = e.clientX - startX;
            panY = e.clientY - startY;
            applyZoom();
        });
        window.addEventListener('pointerup', function() {
            dragging = false;
            applyZoom();
        });
    }
});
</script>
@endpush