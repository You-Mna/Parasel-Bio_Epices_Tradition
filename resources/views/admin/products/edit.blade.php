@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Modifier le produit</h1>
        <p class="admin-page-subtitle">{{ $product->name }}</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.products.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

@if($product->name === 'Parasel-Bio Marinade')
<div class="admin-edit-layout">
    <!-- Section principale - Informations du produit -->
    <div class="main-section">
        <div class="form-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-info-circle"></i>
                    Informations du produit
                </h3>
            </div>
            <div class="card-content">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" class="product-form" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <!-- Informations de base -->
                    <div class="form-section">
                        <h4 class="section-title">Informations de base</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name" class="form-label">Nom du produit *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="form-input" required>
                                @error('name')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="slug" class="form-label">Slug *</label>
                                <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" class="form-input" required>
                                @error('slug')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-textarea" rows="4">{{ old('description', $product->description) }}</textarea>
                            @error('description')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <!-- Image, Prix et Stock - Masqué pour Parasel-Bio Marinade -->
                    @if($product->name !== 'Parasel-Bio Marinade')
                    <div class="form-section">
                        <h4 class="section-title">Image, Prix et Stock</h4>
                        <div class="form-group">
                            <label for="image" class="form-label">Image du produit</label>
                            <input type="file" id="image" name="image" accept="image/*" class="form-input">
                            @if($product->image)
                                <div class="current-image">
                                    <p class="current-image-label">Image actuelle :</p>
                                    <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="current-image-preview">
                                </div>
                            @endif
                            @error('image')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="price" class="form-label">Prix (FCFA) *</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" class="form-input" required>
                                @error('price')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="stock" class="form-label">Stock *</label>
                                <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $product->stock) }}" class="form-input" required>
                                @error('stock')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Options du produit -->
                    <div class="form-section">
                        <h4 class="section-title">Options du produit</h4>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <span class="checkbox-text">Mettre en avant</span>
                            </label>
                        </div>
                    </div>

                    <!-- Gestion des variantes -->
                    @if($product->variants && count($product->variants) > 0)
                    <div class="form-section variants-section">
                        <h4 class="section-title">
                            <i class="fa-solid fa-layer-group"></i>
                            Gestion des variantes
                        </h4>
                        <div class="variants-grid">
                            @foreach($product->variants as $index => $variant)
                            <div class="variant-card" data-variant-index="{{ $index }}">
                                <div class="variant-header">
                                    <h5 class="variant-name">POIDS : {{ $variant['size'] }}</h5>
                                    <button type="button" class="btn-toggle-variant" data-variant-index="{{ $index }}">
                                        <i class="fa-solid fa-edit"></i> Modifier
                                    </button>
                                </div>
                                
                                <div class="variant-display">
                                    <div class="variant-info">
                                        <span class="variant-price">{{ number_format($variant['price'], 0, ',', ' ') }} FCFA</span>
                                        <span class="variant-stock {{ $variant['stock'] > 0 ? 'stock-available' : 'stock-unavailable' }}">
                                            {{ $variant['stock'] > 0 ? 'EN STOCK (' . $variant['stock'] . ')' : 'EN RUPTURE' }}
                                        </span>
                                    </div>
                                    @if(isset($variant['image']) && $variant['image'])
                                    <div class="variant-image">
                                        <img src="{{ asset('images/' . $variant['image']) }}" alt="{{ $variant['size'] }}" class="variant-img">
                                    </div>
                                    @endif
                                </div>

                                <div class="variant-edit-form" style="display: none;">
                                    <div class="variant-form-grid">
                                        <div class="form-group">
                                            <label class="form-label">Taille</label>
                                            <input type="text" name="variants[{{ $index }}][size]" value="{{ $variant['size'] }}" class="form-input variant-size-input">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Prix (FCFA)</label>
                                            <input type="number" name="variants[{{ $index }}][price]" value="{{ $variant['price'] }}" step="0.01" min="0" class="form-input variant-price-input">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="variants[{{ $index }}][stock]" value="{{ $variant['stock'] }}" min="0" class="form-input variant-stock-input">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Image de la variante</label>
                                        <input type="file" name="variants[{{ $index }}][image]" accept="image/*" class="form-input variant-image-input">
                                        @if(isset($variant['image']) && $variant['image'])
                                        <div class="current-variant-image">
                                            <p class="current-image-label">Image actuelle :</p>
                                            <img src="{{ asset('images/' . $variant['image']) }}" alt="{{ $variant['size'] }}" class="current-image-preview">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="form-actions">
                        <button type="submit" class="btn-admin success">
                            <i class="fa-solid fa-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn-admin secondary">
                            <i class="fa-solid fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Section latérale - Actions et statuts -->
    <div class="sidebar-section">
        <!-- Aperçu du produit -->
        <div class="action-card">
            <h3 class="action-title">Aperçu</h3>
            <div class="product-card">
                <div class="product-media">
                    @if($product->variants && count($product->variants) > 0)
                        <!-- Image dynamique pour Parasel-Bio Marinade -->
                        <img id="preview-product-image-{{ $product->id }}" 
                             src="{{ asset('images/parasel115g.jpg') }}" 
                             alt="{{ $product->name }}"
                             data-115g="{{ asset('images/parasel115g.jpg') }}"
                             data-275g="{{ asset('images/parasel275g.jpg') }}"
                             data-850g="{{ asset('images/parasel850g.jpg') }}">
                    @elseif($product->image)
                        <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}">
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
                                    <div class="variant-buttons" data-product-id="preview-{{ $product->id }}">
                                        @foreach($product->variants as $variant)
                                            <button type="button" 
                                                    class="variant-btn {{ $loop->first ? 'active' : '' }}" 
                                                    data-size="{{ $variant['size'] }}" 
                                                    data-price="{{ $variant['price'] }}"
                                                    data-image="{{ 
                                                        $variant['size'] === '115g' ? asset('images/parasel115g.jpg') : 
                                                        ($variant['size'] === '275g' ? asset('images/parasel275g.jpg') : 
                                                        ($variant['size'] === '850g' ? asset('images/parasel850g.jpg') : ''))
                                                    }}"
                                                    data-stock="{{ $variant['stock'] }}">
                                                {{ $variant['size'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="variant-meta" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="variant-price" id="preview-price-{{ $product->id }}">
                                        {{ number_format($product->variants[0]['price'], 0, ',', ' ') }} FCFA
                                    </span>
                                    <span class="variant-stock {{ $product->variants[0]['stock'] > 0 ? 'stock-available' : 'stock-unavailable' }}" id="preview-stock-{{ $product->id }}">
                                        {{ $product->variants[0]['stock'] > 0 ? 'EN STOCK' : 'EN RUPTURE' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <!-- Produit sans variantes -->
                            <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="weight-label">POIDS :</span>
                                <span class="weight-value">
                                    @if($product->name === 'Arôme Parasel')
                                        33cl
                                    @elseif($product->name === 'ParaStress')
                                        850g
                                    @elseif($product->name === 'Xwladjê du Chef Paludier')
                                        850g
                                    @else
                                        850g
                                    @endif
                                </span>
                            </div>
                            
                            <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="weight-label">PRIX :</span>
                                <span class="weight-value">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                <span class="weight-value {{ $product->stock > 0 ? 'stock-available' : 'stock-unavailable' }}" style="margin-left: auto;">{{ $product->stock > 0 ? 'EN STOCK' : 'EN RUPTURE' }}</span>
                            </div>
                        @endif

                        <!-- Sélecteur de quantité -->
                        <div class="quantity-selector">
                            <label class="quantity-label">Quantité :</label>
                            <div class="quantity-modern">
                                <button type="button" class="qty-btn minus" data-product-id="preview-{{ $product->id }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                                <span class="qty-display">1</span>
                                <button type="button" class="qty-btn plus" data-product-id="preview-{{ $product->id }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Bouton Ajouter au panier -->
                        <button type="button" class="add-to-cart" disabled>
                            <i class="fa-solid fa-cart-plus"></i>
                            Ajouter au panier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statut du stock -->
        @php
            $hasVariants2 = $product->variants && is_array($product->variants) && count($product->variants) > 0;
            $variantStock2 = 0;
            if ($hasVariants2) {
                foreach ($product->variants as $variantTmp) {
                    $variantStock2 += (int) ($variantTmp['stock'] ?? 0);
                }
            }
            $effectiveStock = $hasVariants2 ? $variantStock2 : (int) $product->stock;
        @endphp
        <div class="action-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-warehouse"></i>
                    Statut du stock
                </h3>
            </div>
            <div class="card-content">
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
                    $effectiveStock = $hasVariants ? $variantStock : (int) $product->stock;
                @endphp
                <div class="status-display">
                    <span class="status-badge {{ $effectiveStock > 0 ? 'status-active' : 'status-inactive' }}">
                        <i class="fa-solid {{ $effectiveStock > 0 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        {{ $effectiveStock > 0 ? 'En stock' : 'En rupture' }}
                    </span>
                </div>
                <form method="POST" action="{{ route('admin.products.toggle-stock', $product) }}" class="toggle-stock-form">
                    @csrf
                    <button type="submit" class="btn-toggle-stock {{ $effectiveStock > 0 ? 'set-inactive' : 'set-active' }}">
                        <i class="fa-solid {{ $effectiveStock > 0 ? 'fa-minus-circle' : 'fa-plus-circle' }}"></i>
                        {{ $effectiveStock > 0 ? 'Mettre en rupture' : 'Remettre en stock' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Statut du produit -->
        <div class="action-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-toggle-on"></i>
                    Statut du produit
                </h3>
            </div>
            <div class="card-content">
                <div class="status-display">
                    <span class="status-badge {{ $product->status === 'active' ? 'status-active' : 'status-inactive' }}">
                        <i class="fa-solid {{ $product->status === 'active' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        {{ $product->status === 'active' ? 'Actif' : 'Désactivé' }}
                    </span>
                </div>
                <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" class="toggle-status-form">
                    @csrf
                    <button type="submit" class="btn-toggle-status {{ $product->status === 'active' ? 'set-inactive' : 'set-active' }}">
                        <i class="fa-solid {{ $product->status === 'active' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                        {{ $product->status === 'active' ? 'Désactiver le produit' : 'Activer le produit' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@else
<div class="admin-edit-grid">
    <!-- Formulaire de modification -->
    <div class="admin-edit-form">
        <div class="form-card">
            <h3 class="form-title">Informations du produit</h3>
            <form method="POST" action="{{ route('admin.products.update', $product) }}" class="product-form" enctype="multipart/form-data">
    @csrf @method('PUT')
                
                <div class="form-group">
                    <label for="name" class="form-label">Nom du produit *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="form-input" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug *</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" class="form-input" required>
                    @error('slug')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4">{{ old('description', $product->description) }}</textarea>
                    @error('description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Image du produit</label>
                    <input type="file" id="image" name="image" accept="image/*" class="form-input">
                    @if($product->image)
                        <div class="current-image">
                            <p class="current-image-label">Image actuelle :</p>
                            <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="current-image-preview">
                        </div>
                    @endif
                    @error('image')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <!-- Prix et Stock -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="price" class="form-label">Prix (FCFA) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" class="form-input" required>
                        @error('price')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="stock" class="form-label">Stock *</label>
                        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $product->stock) }}" class="form-input" required>
                        @error('stock')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <span class="checkbox-text">Mettre en avant</span>
                        </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin success">
                        <i class="fa-solid fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn-admin secondary">
                        <i class="fa-solid fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="admin-edit-actions">
        <!-- Aperçu du produit -->
        <div class="action-card">
            <h3 class="action-title">Aperçu</h3>
            <div class="product-card" style="border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; overflow: hidden; display: flex; flex-direction: column;">
                <div class="product-media" style="position: relative; background: #f3f4f6; overflow: hidden;">
                    @if($product->image)
                        <img src="{{ Str::startsWith($product->image, ['http://','https://']) ? $product->image : asset('images/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 280px; object-fit: contain; display: block; border-radius: 8px 8px 0 0; background: #f8f9fa; padding: 10px;">
                    @else
                        <div class="placeholder"></div>
                    @endif
                </div>

                <div class="product-body" style="padding: 16px; flex: 1; display: flex; flex-direction: column;">
                    <h3 class="product-title">{{ $product->name }}</h3>
                    <p class="product-desc">{{ Str::limit($product->description, 80) }}</p>

                    <div class="product-meta" style="display: flex; flex-direction: column; gap: 8px; margin: 12px 0 0px 0;">
                        <!-- Produit sans variantes -->
                        <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="weight-label">POIDS :</span>
                            <span class="weight-value">
                                @if($product->name === 'Arôme Parasel')
                                    33cl
                                @elseif($product->name === 'ParaStress')
                                    850g
                                @elseif($product->name === 'Xwladjê du Chef Paludier')
                                    850g
                                @else
                                    850g
                                @endif
                            </span>
                        </div>
                        
                        <div class="product-weight-info" style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="weight-label">PRIX :</span>
                            <span class="weight-value">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                            <span class="weight-value {{ $product->stock > 0 ? 'stock-available' : 'stock-unavailable' }}" style="margin-left: auto;">{{ $product->stock > 0 ? 'EN STOCK' : 'EN RUPTURE' }}</span>
                        </div>

                        <!-- Sélecteur de quantité -->
                        <div class="quantity-selector">
                            <label class="quantity-label">Quantité :</label>
                            <div class="quantity-modern">
                                <button type="button" class="qty-btn minus" data-product-id="preview-{{ $product->id }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                                <span class="qty-display" id="quantity-display-preview-{{ $product->id }}">1</span>
                                <button type="button" class="qty-btn plus" data-product-id="preview-{{ $product->id }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Bouton Ajouter au panier -->
                        <button type="button" class="add-to-cart" disabled>
                            <i class="fa-solid fa-cart-plus"></i>
                            Ajouter au panier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statut du stock -->
        <div class="action-card">
            <h3 class="action-title">Statut du stock</h3>
            <div class="stock-status-display">
                <span class="stock-badge {{ $product->stock > 0 ? 'in-stock' : 'out-of-stock' }}">
                    <i class="fa-solid {{ $product->stock > 0 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        {{ $product->stock > 0 ? 'En stock' : 'En rupture' }}
                </span>
            </div>
            
            <form method="POST" action="{{ route('admin.products.toggle-stock', $product) }}" class="toggle-stock-form">
                @csrf
                <button type="submit" class="btn-toggle-stock {{ $product->stock > 0 ? 'set-out' : 'set-in' }}">
                    <i class="fa-solid {{ $product->stock > 0 ? 'fa-times' : 'fa-check' }}"></i>
                    {{ $product->stock > 0 ? 'Mettre en rupture' : 'Remettre en stock' }}
                </button>
            </form>
        </div>

        <!-- Statut du produit -->
        <div class="action-card">
            <h3 class="action-title">Statut du produit</h3>
            <div class="status-display">
                <span class="status-badge {{ $product->status === 'active' ? 'status-active' : 'status-inactive' }}">
                    <i class="fa-solid {{ $product->status === 'active' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                    {{ $product->status === 'active' ? 'Actif' : 'Désactivé' }}
                </span>
            </div>
            <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" class="toggle-status-form">
                @csrf
                <button type="submit" class="btn-toggle-status {{ $product->status === 'active' ? 'set-inactive' : 'set-active' }}">
                    <i class="fa-solid {{ $product->status === 'active' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                    {{ $product->status === 'active' ? 'Désactiver le produit' : 'Activer le produit' }}
                </button>
</form>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Boutons de modification des variantes
    var buttons = document.querySelectorAll('.btn-toggle-variant');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].onclick = function() {
            var index = this.getAttribute('data-variant-index');
            var item = document.querySelector('[data-variant-index="' + index + '"]');
            var form = item.querySelector('.variant-edit-form');
            var btn = item.querySelector('.btn-toggle-variant');
            
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                btn.innerHTML = '<i class="fa-solid fa-times"></i> Annuler';
                btn.style.backgroundColor = '#dc3545';
                btn.style.color = 'white';
            } else {
                form.style.display = 'none';
                btn.innerHTML = '<i class="fa-solid fa-edit"></i> Modifier';
                btn.style.backgroundColor = '#007bff';
                btn.style.color = 'white';
            }
        };
    }
    
    // Boutons de variantes dans l'aperçu
    var variantBtns = document.querySelectorAll('button[data-size]');
    for (var i = 0; i < variantBtns.length; i++) {
        variantBtns[i].onclick = function() {
            // Retirer active de tous
            var allBtns = document.querySelectorAll('.variant-btn');
            for (var j = 0; j < allBtns.length; j++) {
                allBtns[j].classList.remove('active');
            }
            
            // Ajouter active au cliqué
            this.classList.add('active');
            
            // Changer image
            var img = document.querySelector('.product-media img');
            if (img) {
                var size = this.getAttribute('data-size');
                if (size === '115g') {
                    img.src = '/images/parasel115g.jpg';
                } else if (size === '275g') {
                    img.src = '/images/parasel275g.jpg';
                } else if (size === '850g') {
                    img.src = '/images/parasel850g.jpg';
                }
            }
            
            // Changer prix et stock
            var productId = this.closest('.variant-buttons').getAttribute('data-product-id');
            var priceId = 'preview-price-' + productId.replace('preview-', '');
            var stockId = 'preview-stock-' + productId.replace('preview-', '');
            
            var priceEl = document.getElementById(priceId);
            var stockEl = document.getElementById(stockId);
            
            if (priceEl) {
                var price = this.getAttribute('data-price');
                priceEl.textContent = price + ' FCFA';
            }
            
            if (stockEl) {
                var stock = this.getAttribute('data-stock');
                var stockValue = parseInt(stock);
                stockEl.textContent = stockValue > 0 ? 'EN STOCK' : 'EN RUPTURE';
                stockEl.className = 'variant-stock ' + (stockValue > 0 ? 'stock-available' : 'stock-unavailable');
            }
        };
    }
});

</script>

@endsection
