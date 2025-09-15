@extends('layouts.app')

@section('content')
<div class="cart-page {{ empty($cartItems) ? 'cart-empty' : 'cart-has-items' }}">
    <div class="cart-container">
        <!-- Header professionnel -->
        <h1 class="cart-title">Votre panier</h1>

        @if(empty($cartItems))
            <!-- Panier vide avec design moderne -->
            <div class="empty-cart">
                <div class="empty-cart-illustration">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <div class="empty-cart-content">
                    <h2>Votre panier est vide</h2>
                    <p>Commencez votre expérience culinaire en découvrant nos épices d'exception</p>
                    <div class="empty-cart-actions">
                        <a href="/produits" class="btn btn-primary btn-large">
                            <i class="fas fa-store"></i>
                            Découvrir nos produits
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Section des produits sélectionnés - Design moderne -->
            <div class="cart-content">
                <div class="cart-header-info">
                    <div class="cart-stats">
                        <span class="items-count">Produits sélectionnés</span>
                        <span class="total-preview">{{ count($cartItems) }}</span>
                                    </div>
                                </div>
                                
                <div class="cart-items-modern">
                    @foreach($cartItems as $item)
                        <div class="cart-item-modern" data-cart-key="{{ $item['cart_key'] }}" data-unit-price="{{ $item['variant_price'] }}">
                            <div class="item-image-modern">
                                @if($item['product']->name === 'Parasel-Bio Marinade')
                                @if($item['variant_size'] === '115g')
                                                <img src="{{ asset('images/parasel115g.jpg') }}" alt="{{ $item['product']->name }}">
                                @elseif($item['variant_size'] === '275g')
                                                <img src="{{ asset('images/parasel275g.jpg') }}" alt="{{ $item['product']->name }}">
                                @elseif($item['variant_size'] === '850g')
                                    <img src="{{ asset('images/parasel850g.jpg') }}" alt="{{ $item['product']->name }}">
                                            @else
                                                <img src="{{ asset('images/parasel115g.jpg') }}" alt="{{ $item['product']->name }}">
                                            @endif
                                @elseif($item['product']->image)
                                    <img src="{{ Str::startsWith($item['product']->image, ['http://','https://']) ? $item['product']->image : asset('images/' . $item['product']->image) }}" alt="{{ $item['product']->name }}">
                                @else
                                    <div class="placeholder-icon">🌶️</div>
                                @endif
                            </div>
                            
                            <div class="item-info-modern">
                                <h3 class="item-title">
                                    {{ $item['product']->name }}
                                    @if(!empty($item['variant_size']))
                                        <span class="variant-size">{{ $item['variant_size'] }}</span>
                                    @endif
                                </h3>
                                <p class="item-desc">{{ Str::limit($item['product']->description, 60) }}</p>
                                <div class="item-price-info">
                                    <span class="unit-price">{{ number_format($item['variant_price'], 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                            
                            <div class="item-controls-modern">
                                <div class="quantity-modern">
                                    <button type="button" class="qty-btn" onclick="updateQuantity('{{ $item['cart_key'] }}', -1)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                    <span class="qty-display">{{ $item['quantity'] }}</span>
                                    <button type="button" class="qty-btn" onclick="updateQuantity('{{ $item['cart_key'] }}', 1)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="item-total-modern">
                                    <span class="total-price">{{ number_format($item['variant_price'] * $item['quantity'], 0, ',', ' ') }} FCFA</span>
                                </div>
                                
                                <button class="remove-btn" onclick="removeItem('{{ $item['cart_key'] }}')" title="Supprimer">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3,6 5,6 21,6"></polyline>
                                        <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Ligne de total -->
                <div class="cart-total-line">
                    <div class="total-label">Total à payer :</div>
                    <div class="total-amount">{{ number_format($total, 0, ',', ' ') }} FCFA</div>
                </div>
                
            </div>
            
            <!-- Section validation de commande -->
            <div class="checkout-section">
                <div class="checkout-container">
                    <div class="checkout-actions">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-continue">
                            <i class="fas fa-shopping-bag"></i>
                            Continuer vos achats
                        </a>
                        <a href="{{ route('checkout') }}" class="btn btn-primary btn-checkout">
                            <i class="fas fa-arrow-right"></i>
                            Passer commande
                        </a>
                        </div>
                </div>
            </div>
            
        @endif
    </div>
</div>

<script>
function updateQuantity(cartKey, change) {
    var itemEl = document.querySelector('[data-cart-key="' + cartKey + '"]');
    var display = itemEl ? itemEl.querySelector('.qty-display') : null;
    if (display) {
        var currentValue = parseInt(display.textContent);
        var newValue = currentValue + change;
        if (newValue >= 1 && newValue <= 99) {
            var unit = parseFloat(itemEl.getAttribute('data-unit-price')) || 0;
            var oldTotal = unit * currentValue;
            var newTotal = unit * newValue;
            var totalChange = newTotal - oldTotal;
            
            // Mettre à jour l'affichage immédiatement
            display.textContent = newValue;
            
            // Recalculer le total de la ligne immédiatement côté client
            var totalAmountEl = itemEl.querySelector('.total-price');
            if (totalAmountEl) {
                totalAmountEl.textContent = newTotal.toLocaleString('fr-FR') + ' FCFA';
            }
            
            // Mettre à jour le total général
            updateGrandTotal(totalChange);
            
            // Envoyer la requête au serveur
            fetch('/panier/modifier-quantite/' + cartKey, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: newValue })
            }).finally(function() {
                recalcCartTotal();
            });
        }
    }
}

function removeItem(cartKey) {
    showRemoveConfirmation(cartKey);
}

function showRemoveConfirmation(cartKey) {
    // Créer le toast de confirmation
    const toast = document.createElement('div');
    toast.className = 'confirmation-toast';
    toast.innerHTML = `
        <div class="confirmation-content">
            <div class="confirmation-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                </svg>
            </div>
            <div class="confirmation-text">
                <div class="confirmation-message">Voulez-vous supprimer ce produit du panier ?</div>
            </div>
            <div class="confirmation-actions">
                <button class="btn-confirm" onclick="confirmRemove(true, '${cartKey}')">Oui</button>
                <button class="btn-cancel" onclick="confirmRemove(false, '${cartKey}')">Non</button>
            </div>
        </div>
    `;
    
    // Ajouter les styles
    toast.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        z-index: 10000;
        padding: 24px;
        min-width: 320px;
        max-width: 400px;
    `;
    
    // Styles pour le contenu
    const content = toast.querySelector('.confirmation-content');
    content.style.cssText = `
        display: flex;
        align-items: center;
        gap: 16px;
    `;
    
    // Styles pour l'icône
    const icon = toast.querySelector('.confirmation-icon');
    icon.style.cssText = `
        color: #ef4444;
        flex-shrink: 0;
    `;
    
    // Styles pour le texte
    const text = toast.querySelector('.confirmation-text');
    text.style.cssText = `
        flex: 1;
    `;
    
    const message = toast.querySelector('.confirmation-message');
    message.style.cssText = `
        font-weight: 600;
        font-size: 16px;
        color: #111827;
    `;
    
    // Styles pour les actions
    const actions = toast.querySelector('.confirmation-actions');
    actions.style.cssText = `
        display: flex;
        gap: 8px;
        margin-top: 16px;
    `;
    
    var btnConfirm = toast.querySelector('.btn-confirm');
    if (btnConfirm) {
        btnConfirm.style.cssText = 
            'background: #ef4444;' +
            'color: white;' +
            'border: none;' +
            'padding: 8px 16px;' +
            'border-radius: 6px;' +
            'font-size: 14px;' +
            'font-weight: 500;' +
            'cursor: pointer;' +
            'transition: background-color 0.2s;';
    }
    
    var btnCancel = toast.querySelector('.btn-cancel');
    if (btnCancel) {
        btnCancel.style.cssText = 
            'background: #f3f4f6;' +
            'color: #374151;' +
            'border: none;' +
            'padding: 8px 16px;' +
            'border-radius: 6px;' +
            'font-size: 14px;' +
            'font-weight: 500;' +
            'cursor: pointer;' +
            'transition: background-color 0.2s;';
    }
    
    // Ajouter au DOM
    document.body.appendChild(toast);
    
    // Ajouter un overlay
    var overlay = document.createElement('div');
    overlay.style.cssText = 
        'position: fixed;' +
        'top: 0;' +
        'left: 0;' +
        'right: 0;' +
        'bottom: 0;' +
        'background: rgba(0, 0, 0, 0.5);' +
        'z-index: 9999;';
    document.body.appendChild(overlay);
    
    // Stocker les références
    window.removeToast = toast;
    window.removeOverlay = overlay;
}

function confirmRemove(confirmed, cartKey) {
    // Supprimer le toast et l'overlay
    if (window.removeToast) {
        window.removeToast.remove();
    }
    if (window.removeOverlay) {
        window.removeOverlay.remove();
    }
    
    if (confirmed) {
        // Créer un formulaire pour la suppression
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/panier/supprimer/' + cartKey;
        
        // Ajouter le token CSRF
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    }
}

function recalcCartTotal() {
    // Mettre à jour le nombre de produits dans l'en-tête
    var totalPreview = document.querySelector('.total-preview');
    if (totalPreview) {
        var itemCount = document.querySelectorAll('.cart-item-modern').length;
        totalPreview.textContent = itemCount;
    }
}

function updateGrandTotal(change) {
    var totalAmountEl = document.querySelector('.total-amount');
    if (totalAmountEl) {
        var currentTotal = parseFloat(totalAmountEl.textContent.replace(/[^\d]/g, '')) || 0;
        var newTotal = Math.max(0, currentTotal + change);
        totalAmountEl.textContent = newTotal.toLocaleString('fr-FR') + ' FCFA';
    }
}

function checkEmptyCart() {
    var itemCount = document.querySelectorAll('.cart-item-modern').length;
    if (itemCount === 0) {
        // Rediriger vers la page panier vide après un court délai
        setTimeout(function() {
            window.location.reload();
        }, 500);
    }
}

function clearCart() {
    if (confirm('Êtes-vous sûr de vouloir vider complètement votre panier ?')) {
        fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(function(response) {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

// Gestion du formulaire de validation
var paymentMethodSelect = document.getElementById('payment_method');
if (paymentMethodSelect) {
    paymentMethodSelect.addEventListener('change', function() {
    var phoneGroup = document.getElementById('phone_group');
    var referenceGroup = document.getElementById('reference_group');
    var instructionsGroup = document.getElementById('payment_instructions');
    var phoneInput = document.getElementById('phone_number');
    var referenceInput = document.getElementById('payment_reference');
    
    // Masquer tous les groupes par défaut
    if (phoneGroup) phoneGroup.style.display = 'none';
    if (referenceGroup) referenceGroup.style.display = 'none';
    if (instructionsGroup) instructionsGroup.style.display = 'none';
    if (phoneInput) phoneInput.required = false;
    if (referenceInput) referenceInput.required = false;
    
    // Afficher les champs selon le mode de paiement sélectionné
    if (this.value === 'mtn_momo' || this.value === 'moov_money' || this.value === 'celtiis_money') {
        if (phoneGroup) phoneGroup.style.display = 'block';
        if (referenceGroup) referenceGroup.style.display = 'block';
        if (instructionsGroup) instructionsGroup.style.display = 'block';
        if (phoneInput) phoneInput.required = true;
        if (referenceInput) referenceInput.required = true;
    }
    });
}

// Système de confirmation élégant
var pendingCartKey = null;

function confirmRemoveItem(cartKey, productName) {
    console.log('confirmRemoveItem appelée avec:', cartKey, productName);
    pendingCartKey = cartKey;
    
    // Créer le toast de confirmation
    var toast = document.createElement('div');
    toast.className = 'confirmation-toast';
    toast.innerHTML = 
        '<div class="confirmation-content">' +
            '<div class="confirmation-icon">' +
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                    '<path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>' +
                '</svg>' +
            '</div>' +
            '<div class="confirmation-text">' +
                '<div class="confirmation-title">Confirmer la suppression</div>' +
                '<div class="confirmation-message">Voulez-vous supprimer "' + productName + '" du panier ?</div>' +
            '</div>' +
            '<div class="confirmation-actions">' +
                '<button class="btn-confirm" onclick="confirmAction(true)">Oui</button>' +
                '<button class="btn-cancel" onclick="confirmAction(false)">Non</button>' +
            '</div>' +
        '</div>';
    
    // Ajouter les styles
    toast.style.cssText = 
        'position: fixed;' +
        'top: 50%;' +
        'left: 50%;' +
        'transform: translate(-50%, -50%);' +
        'background: white;' +
        'border-radius: 12px;' +
        'box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);' +
        'z-index: 10000;' +
        'padding: 24px;' +
        'min-width: 320px;' +
        'max-width: 400px;';
    
    // Styles pour le contenu
    var content = toast.querySelector('.confirmation-content');
    content.style.cssText = 
        'display: flex;' +
        'align-items: center;' +
        'gap: 16px;';
    
    // Styles pour l'icône
    var icon = toast.querySelector('.confirmation-icon');
    if (icon) {
        icon.style.cssText = 
            'color: #ef4444;' +
            'flex-shrink: 0;';
    }
    
    // Styles pour le texte
    var text = toast.querySelector('.confirmation-text');
    if (text) {
        text.style.cssText = 'flex: 1;';
    }
    
    var title = toast.querySelector('.confirmation-title');
    if (title) {
        title.style.cssText = 
            'font-weight: 600;' +
            'font-size: 16px;' +
            'color: #111827;' +
            'margin-bottom: 4px;';
    }
    
    var message = toast.querySelector('.confirmation-message');
    if (message) {
        message.style.cssText = 
            'font-size: 14px;' +
            'color: #6b7280;';
    }
    
    // Styles pour les actions
    var actions = toast.querySelector('.confirmation-actions');
    if (actions) {
        actions.style.cssText = 
            'display: flex;' +
            'gap: 8px;' +
            'margin-top: 16px;';
    }
    
    var btnConfirm = toast.querySelector('.btn-confirm');
    if (btnConfirm) {
        btnConfirm.style.cssText = 
            'background: #ef4444;' +
            'color: white;' +
            'border: none;' +
            'padding: 8px 16px;' +
            'border-radius: 6px;' +
            'font-size: 14px;' +
            'font-weight: 500;' +
            'cursor: pointer;' +
            'transition: background-color 0.2s;';
    }
    
    var btnCancel = toast.querySelector('.btn-cancel');
    if (btnCancel) {
        btnCancel.style.cssText = 
            'background: #f3f4f6;' +
            'color: #374151;' +
            'border: none;' +
            'padding: 8px 16px;' +
            'border-radius: 6px;' +
            'font-size: 14px;' +
            'font-weight: 500;' +
            'cursor: pointer;' +
            'transition: background-color 0.2s;';
    }
    
    // Ajouter au DOM
    document.body.appendChild(toast);
    
    // Ajouter un overlay
    var overlay = document.createElement('div');
    overlay.style.cssText = 
        'position: fixed;' +
        'top: 0;' +
        'left: 0;' +
        'right: 0;' +
        'bottom: 0;' +
        'background: rgba(0, 0, 0, 0.5);' +
        'z-index: 9999;';
    document.body.appendChild(overlay);
    
    // Stocker les références
    window.confirmationToast = toast;
    window.confirmationOverlay = overlay;
}

function confirmAction(confirmed) {
    // Supprimer le toast et l'overlay
    if (window.confirmationToast) {
        window.confirmationToast.remove();
    }
    if (window.confirmationOverlay) {
        window.confirmationOverlay.remove();
    }
    
    if (confirmed && pendingCartKey) {
        removeItem(pendingCartKey);
    }
    
    pendingCartKey = null;
}
</script>
@endsection







