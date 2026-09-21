@extends('layouts.app')

@section('content')
<div class="checkout-page checkout-pay-page">
    <div class="checkout-container">
        <div class="checkout-pay-head">
            <h1 class="checkout-title checkout-pay-title">Finaliser votre paiement</h1>
            <p class="checkout-pay-intro">Montant à payer : {{ number_format($total, 0, ',', ' ') }} FCFA</p>
            <p class="checkout-pay-hint">Simulation Mobile Money. Utilisez le numéro test <strong>0164000001</strong> pour effectuer la transaction (ne mettez pas votre vrai numéro).</p>
        </div>

        <div id="fedapay-embed-wrapper" class="fedapay-embed-wrapper fedapay-embed-visible-mobile" aria-hidden="true">
            <div class="fedapay-embed-header">
                <h3 class="fedapay-embed-title">Paiement en ligne</h3>
                <a href="{{ route('payment.cancelled') }}" class="fedapay-embed-close" aria-label="Annuler">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <div class="fedapay-embed-inner">
                <div id="fedapay-embed" class="fedapay-embed-container"></div>
                <div class="fedapay-embed-footer-mask" aria-hidden="true"></div>
            </div>
        </div>

        <div id="fedapay-desktop-placeholder" class="fedapay-desktop-placeholder fedapay-desktop-only">
            <p>Chargement du formulaire de paiement...</p>
        </div>
        <div id="checkout-pay-loader" class="checkout-pay-loader">
            <span class="btn-spinner" aria-hidden="true"></span>
            <span>Ouverture du module de paiement...</span>
        </div>

        <div class="checkout-pay-actions">
            <a href="{{ route('payment.cancelled') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </div>
</div>

<script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const publicKey = '{{ config('services.fedapay.public_key') }}';
    const environment = '{{ config('services.fedapay.environment', 'sandbox') }}';
    const orderId = {{ $order->id }};
    const total = {{ $total }};
    const pageLoader = document.getElementById('checkout-pay-loader');

    function hidePayLoader() {
        if (pageLoader) pageLoader.classList.add('hidden');
    }

    if (!publicKey) {
        document.getElementById('fedapay-desktop-placeholder').innerHTML = '<p class="error-message">Le paiement en ligne n\'est pas disponible pour le moment.</p>';
        hidePayLoader();
        return;
    }

    const totalAmount = Math.max(1, Math.round(Number(total)));
    const baseConfig = {
        public_key: publicKey,
        environment: environment,
        transaction: {
            amount: totalAmount,
            description: 'Commande Parasel Bio ' + orderId,
            currency: { iso: 'XOF' },
            custom_metadata: { order_id: String(orderId) }
        },
        customer: {
            email: '{{ auth()->user()->email }}',
            lastname: '{{ auth()->user()->last_name ?? 'Client' }}',
            firstname: '{{ auth()->user()->first_name ?? 'Parasel' }}',
            phone_number: {
                country: 'BJ'@if(!empty($phoneForFedaPay)),
                number: '{{ $phoneForFedaPay }}'@endif
            }
        },
        onComplete: function (data) {
            if (data.reason === FedaPay.CHECKOUT_COMPLETED) {
                window.location.href = '{{ route('client.orders') }}?payment=success';
            } else {
                window.location.href = '{{ route('payment.cancelled') }}';
            }
        }
    };

    function isMobileDevice() {
        return window.matchMedia('(max-width: 768px)').matches ||
               (typeof window.orientation !== 'undefined') ||
               /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    if (isMobileDevice()) {
        const wrapper = document.getElementById('fedapay-embed-wrapper');
        const container = document.getElementById('fedapay-embed');
        wrapper.setAttribute('aria-hidden', 'false');
        // Petit délai pour que le conteneur soit bien rendu avant l’injection FedaPay
        requestAnimationFrame(function() {
            FedaPay.init(Object.assign({}, baseConfig, { container: '#fedapay-embed' }));
            setTimeout(hidePayLoader, 1200);
        });
    } else {
        document.getElementById('fedapay-desktop-placeholder').style.display = 'none';
        const widget = FedaPay.init(baseConfig);
        widget.open();
        setTimeout(hidePayLoader, 1200);
        document.getElementById('fedapay-desktop-placeholder').innerHTML = '<p>Si la fenêtre de paiement ne s\'est pas ouverte, <a href="{{ route('payment.cancelled') }}">cliquez ici pour annuler</a>.</p>';
        document.getElementById('fedapay-desktop-placeholder').style.display = 'block';
    }
});
</script>
@endsection
