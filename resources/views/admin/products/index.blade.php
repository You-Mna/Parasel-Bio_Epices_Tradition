@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Gestion des Produits</h1>
        <p class="admin-page-subtitle">Gérez votre catalogue de produits Parasel-Bio</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('products.index') }}" target="_blank" class="btn-admin secondary">
            <i class="fas fa-external-link-alt"></i> Voir la boutique
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn-admin success">
            <i class="fa-solid fa-plus"></i> Ajouter un produit
        </a>
    </div>
</div>

<!-- Liste des produits -->
<div class="admin-products-grid">
    @foreach($products as $product)
        <div class="product-card-admin">
            <!-- Image du produit -->
            <div class="product-image-admin">
                @if($product->image)
                    <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="product-img">
                @else
                    <div class="product-placeholder">
                        <i class="fa-solid fa-box"></i>
                    </div>
                @endif
            </div>

            <!-- Informations du produit -->
            <div class="product-info-admin">
                <h3 class="product-name-admin">{{ $product->name }}</h3>
                
                <!-- Statut du stock : seules les variantes de Parasel-Bio Marinade sont prises en compte -->
                        @php
                            $hasVariants = $product->name === 'Parasel-Bio Marinade'
                                && $product->variants
                                && is_array($product->variants)
                                && count($product->variants) > 0;
                            $variantStock = 0;
                            if ($hasVariants) {
                                foreach ($product->variants as $variant) {
                                    $variantStock += (int) ($variant['stock'] ?? 0);
                                }
                            }
                            $productStock = (int) ($product->stock ?? 0);
                            $effectiveStock = $hasVariants ? $variantStock : $productStock;
                        @endphp
                        <div class="stock-status-admin">
                            <span class="stock-badge {{ $effectiveStock > 0 ? 'in-stock' : 'out-of-stock' }}">
                                <i class="fa-solid {{ $effectiveStock > 0 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $effectiveStock > 0 ? 'En stock' : 'En rupture' }}
                            </span>
                        </div>

                <!-- Actions rapides -->
                <div class="product-actions-admin">
                    <!-- Actions principales -->
                    <div class="product-main-actions">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-admin small">
                            <i class="fa-solid fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Pagination -->
@if($products->hasPages())
    <div class="admin-pagination admin-table-pagination">
        {{ $products->links('vendor.pagination.admin') }}
    </div>
@endif
@endsection

@push('scripts')
<script>
// Animation pour le toggle de statut
document.querySelectorAll('.toggle-stock-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('.btn-toggle-stock');
        const originalText = button.innerHTML;
        
        // Animation de chargement
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mise à jour...';
        button.disabled = true;
        
        // Laisser le formulaire se soumettre normalement
        // L'animation sera réinitialisée par le rechargement de la page
    });
});
</script>
@endpush

