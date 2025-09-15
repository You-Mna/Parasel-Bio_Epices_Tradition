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
                
                <form id="checkout-form" class="checkout-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="payment_method" class="form-label">Mode de paiement *</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="mtn_momo">MTN</option>
                            <option value="moov_money">Moov</option>
                            <option value="celtiis_money">Celtiis</option>
                            <option value="cash">Paiement à la livraison</option>
                        </select>
                        <span class="error-message" id="payment_method_error"></span>
                    </div>

                    <div class="form-group" id="phone_group" style="display: none;">
                        <label for="phone_number" class="form-label">Numéro de téléphone *</label>
                        <input type="tel" name="phone_number" id="phone_number" class="form-input" placeholder="Ex: 0701234567">
                        <span class="error-message" id="phone_number_error"></span>
                    </div>

                    <div class="form-group" id="reference_group" style="display: none;">
                        <label for="payment_reference" class="form-label">Référence de paiement *</label>
                        <input type="text" name="payment_reference" id="payment_reference" class="form-input" placeholder="Ex: TXN123456789">
                        <span class="error-message" id="payment_reference_error"></span>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary">Retour au panier</a>
                        <button type="submit" class="btn btn-primary btn-large" id="submit-btn">
                            <i class="fas fa-check"></i>
                            <span class="btn-text">Confirmer la commande</span>
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
    const paymentMethod = document.getElementById('payment_method');
    const phoneGroup = document.getElementById('phone_group');
    const referenceGroup = document.getElementById('reference_group');
    const phoneInput = document.getElementById('phone_number');
    const referenceInput = document.getElementById('payment_reference');
    const checkoutForm = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    // Store order ID for payment processing
    let currentOrderId = null;

    paymentMethod.addEventListener('change', function() {
        const value = this.value;
        
        if (value === 'mtn_momo' || value === 'moov_money' || value === 'orange_money') {
            phoneGroup.style.display = 'block';
            referenceGroup.style.display = 'none'; // Hide reference field for API payments
            phoneInput.required = true;
            referenceInput.required = false;
        } else if (value === 'cash') {
            phoneGroup.style.display = 'none';
            referenceGroup.style.display = 'none';
            phoneInput.required = false;
            referenceInput.required = false;
            phoneInput.value = '';
            referenceInput.value = '';
        } else {
            phoneGroup.style.display = 'none';
            referenceGroup.style.display = 'none';
            phoneInput.required = false;
            referenceInput.required = false;
            phoneInput.value = '';
            referenceInput.value = '';
        }
    });

    checkoutForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(checkoutForm);
        const paymentMethodValue = formData.get('payment_method');
        
        // Clear previous errors
        clearErrors();
        
        // Validate form
        if (!paymentMethodValue) {
            showError('payment_method_error', 'Veuillez sélectionner un mode de paiement');
            return;
        }

        if (paymentMethodValue === 'cash') {
            // Handle cash payment
            await processCashPayment(formData);
        } else {
            // Handle mobile money payment
            await processMobileMoneyPayment(formData);
        }
    });

    async function processCashPayment(formData) {
        try {
            setLoading(true);
            
            const response = await fetch('{{ route("order.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payment_method: 'cash',
                    delivery_address: 'Adresse de livraison à confirmer',
                    notes: 'Paiement à la livraison',
                    phone_number: '',
                    payment_reference: ''
                })
            });

            const result = await response.json();
            
            if (response.ok) {
                window.location.href = '{{ route("client.orders") }}';
            } else {
                showError('payment_method_error', result.message || 'Erreur lors de la création de la commande');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('payment_method_error', 'Erreur de connexion');
        } finally {
            setLoading(false);
        }
    }

    async function processMobileMoneyPayment(formData) {
        try {
            setLoading(true);
            
            // First, create the order
            const orderResponse = await fetch('{{ route("order.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payment_method: formData.get('payment_method'),
                    delivery_address: 'Adresse de livraison à confirmer',
                    notes: 'Paiement mobile money',
                    phone_number: formData.get('phone_number'),
                    payment_reference: ''
                })
            });

            const orderResult = await orderResponse.json();
            
            if (!orderResponse.ok) {
                showError('payment_method_error', orderResult.message || 'Erreur lors de la création de la commande');
                return;
            }

            currentOrderId = orderResult.order_id;

            // Then initialize payment
            const paymentResponse = await fetch('{{ route("payment.initialize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    order_id: currentOrderId,
                    payment_method: formData.get('payment_method'),
                    phone_number: formData.get('phone_number')
                })
            });

            const paymentResult = await paymentResponse.json();
            
            if (paymentResponse.ok && paymentResult.success) {
                // Redirect to payment URL or show payment instructions
                if (paymentResult.payment_url) {
                    window.location.href = paymentResult.payment_url;
                } else {
                    // Show payment instructions
                    showPaymentInstructions(paymentResult);
                }
            } else {
                showError('payment_method_error', paymentResult.error || 'Erreur lors de l\'initialisation du paiement');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('payment_method_error', 'Erreur de connexion');
        } finally {
            setLoading(false);
        }
    }

    function showPaymentInstructions(paymentData) {
        // Create a modal or alert with payment instructions
        const instructions = `
            <div class="payment-instructions-modal">
                <h3>Instructions de paiement</h3>
                <p>Votre commande a été créée avec succès !</p>
                <p><strong>Référence:</strong> ${paymentData.reference}</p>
                <p><strong>Montant:</strong> {{ number_format($total, 0, ',', ' ') }} FCFA</p>
                <p>Veuillez effectuer le paiement via votre application mobile money.</p>
                <button onclick="checkPaymentStatus('${paymentData.transaction_id}')" class="btn btn-primary">
                    Vérifier le paiement
                </button>
            </div>
        `;
        
        // You can implement a modal here or redirect to a payment status page
        alert('Paiement initialisé ! Référence: ' + paymentData.reference);
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
        });
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    function setLoading(loading) {
        if (loading) {
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            submitBtn.disabled = true;
        } else {
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    // Global function to check payment status
    window.checkPaymentStatus = async function(transactionId) {
        try {
            const response = await fetch(`{{ route("payment.status", ":id") }}`.replace(':id', transactionId));
            const result = await response.json();
            
            if (result.success && result.status === 'approved') {
                window.location.href = '{{ route("client.orders") }}';
            } else {
                alert('Paiement en cours... Veuillez patienter.');
            }
        } catch (error) {
            console.error('Error checking payment status:', error);
        }
    };
});
</script>
@endsection
