@extends('layouts.app')

@section('content')
<div class="checkout-page">
    <div class="checkout-container">
        <!-- Header professionnel -->
        <h1 class="checkout-title">Finaliser votre commande</h1>

        <div class="checkout-content">
            <!-- Résumé de la commande -->
            <div class="order-summary">
                <h2 class="summary-title">Résumé de votre commande</h2>
                
                <div class="order-items">
                    @foreach($cartItems as $item)
                        <div class="order-item">
                            <div class="item-image">
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
                                    <div class="placeholder"></div>
                                @endif
                            </div>
                            
                            <div class="item-details">
                                <h3 class="item-title">
                                    {{ $item['product']->name }}
                                    @if(!empty($item['variant_size']))
                                        <span class="variant-size">{{ $item['variant_size'] }}</span>
                                    @endif
                                </h3>
                                <p class="item-desc">{{ Str::limit($item['product']->description, 60) }}</p>
                                <div class="item-meta">
                                    <span class="quantity">Quantité: {{ $item['quantity'] }}</span>
                                    <span class="total-price">{{ number_format($item['variant_price'] * $item['quantity'], 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                            
                            <div class="item-total">
                                <span class="unit-price">{{ number_format($item['variant_price'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="order-total">
                    <div class="total-line">
                        <span class="total-label">Total à payer :</span>
                        <span class="total-amount">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Formulaire de commande -->
            <div class="checkout-form-section">
                <h2 class="form-title">Informations de paiement</h2>
                
                <form id="checkout-form" class="checkout-form" action="{{ route('order.process') }}" method="POST" data-manual-loader>
                    @csrf
                    <input type="hidden" name="delivery_address" value="Adresse de livraison à confirmer">
                    <input type="hidden" name="notes" value="">
                    
                    <div class="form-group">
                        <label for="payment_method" class="form-label">Mode de paiement *</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="fedapay">Paiement en ligne</option>
                            <option value="cash">Paiement à la livraison</option>
                        </select>
                        <span class="error-message" id="payment_method_error"></span>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary">Retour au panier</a>
                        <button type="submit" class="btn btn-primary btn-large" id="submit-btn" data-loading-text="Traitement...">
                            <i class="fas fa-check"></i>
                            <span class="btn-text">Confirmer et payer</span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                                Traitement...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
    const btnLoading = submitBtn ? submitBtn.querySelector('.btn-loading') : null;

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => { el.textContent = ''; });
    }
    function showError(elementId, message) {
        const el = document.getElementById(elementId);
        if (el) el.textContent = message;
    }

    checkoutForm.addEventListener('submit', function(e) {
        const formData = new FormData(checkoutForm);
        const paymentMethodValue = formData.get('payment_method');
        clearErrors();
        if (!paymentMethodValue) {
            e.preventDefault();
            showError('payment_method_error', 'Veuillez sélectionner un mode de paiement');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('is-loading');
            if (btnText) btnText.style.display = 'none';
            if (btnLoading) btnLoading.style.display = 'inline-flex';
        }

        if (paymentMethodValue === 'cash') {
            document.querySelector('input[name="notes"]').value = 'Paiement à la livraison';
        } else {
            document.querySelector('input[name="notes"]').value = 'Paiement en ligne (FedaPay)';
        }
    });
});
</script>
@endsection
