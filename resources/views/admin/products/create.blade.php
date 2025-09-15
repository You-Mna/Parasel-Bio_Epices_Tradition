@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Nouveau produit</h1>
        <p class="admin-page-subtitle">Ajoutez un nouveau produit à votre catalogue</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.products.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<div class="admin-edit-grid">
    <!-- Formulaire de création -->
    <div class="admin-edit-form">
        <div class="form-card">
            <h3 class="form-title">Informations du produit</h3>
            <form method="POST" action="{{ route('admin.products.store') }}" class="product-form" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="name" class="form-label">Nom du produit *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug *</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="form-input" required>
                    @error('slug')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4">{{ old('description') }}</textarea>
                    @error('description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Image du produit</label>
                    <input type="file" id="image" name="image" accept="image/*" class="form-input">
                    @error('image')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price" class="form-label">Prix (FCFA) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" class="form-input" required>
                        @error('price')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="stock" class="form-label">Stock *</label>
                        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', 10) }}" class="form-input" required>
                        @error('stock')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <span class="checkbox-text">Mettre en avant</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin primary">
                        <i class="fa-solid fa-plus"></i> Créer le produit
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn-admin secondary">
                        <i class="fa-solid fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Aide et conseils -->
    <div class="admin-edit-actions">
        <!-- Conseils -->
        <div class="action-card">
            <h3 class="action-title">Conseils</h3>
            <div class="tips-content">
                <div class="tip-item">
                    <i class="fa-solid fa-lightbulb"></i>
                    <div>
                        <strong>Nom du produit :</strong> Soyez descriptif et attractif
                    </div>
                </div>
                <div class="tip-item">
                    <i class="fa-solid fa-link"></i>
                    <div>
                        <strong>Slug :</strong> URL-friendly, sans espaces ni caractères spéciaux
                    </div>
                </div>
                <div class="tip-item">
                    <i class="fa-solid fa-image"></i>
                    <div>
                        <strong>Image :</strong> Utilisez une URL d'image de qualité
                    </div>
                </div>
                <div class="tip-item">
                    <i class="fa-solid fa-tag"></i>
                    <div>
                        <strong>Prix :</strong> Indiquez le prix en FCFA
                    </div>
                </div>
            </div>
        </div>

        <!-- Aperçu -->
        <div class="action-card">
            <h3 class="action-title">Aperçu</h3>
            <div class="product-preview">
                <div class="preview-image">
                    <div class="preview-placeholder">
                        <i class="fa-solid fa-box"></i>
                    </div>
                </div>
                <div class="preview-info">
                    <h4 class="preview-name">Nom du produit</h4>
                    <p class="preview-price">0 FCFA</p>
                    <p class="preview-description">Description du produit...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Mise à jour de l'aperçu en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const priceInput = document.getElementById('price');
    const descriptionInput = document.getElementById('description');
    const imageInput = document.getElementById('image');
    
    const previewName = document.querySelector('.preview-name');
    const previewPrice = document.querySelector('.preview-price');
    const previewDescription = document.querySelector('.preview-description');
    const previewImage = document.querySelector('.preview-image');
    
    function updatePreview() {
        previewName.textContent = nameInput.value || 'Nom du produit';
        previewPrice.textContent = (priceInput.value ? Number(priceInput.value).toLocaleString() : '0') + ' FCFA';
        previewDescription.textContent = descriptionInput.value || 'Description du produit...';
        
        if (imageInput.value) {
            previewImage.innerHTML = `<img src="${imageInput.value.startsWith('http') ? imageInput.value : '/images/' + imageInput.value}" alt="${nameInput.value}" class="preview-img">`;
        } else {
            previewImage.innerHTML = '<div class="preview-placeholder"><i class="fa-solid fa-box"></i></div>';
        }
    }
    
    nameInput.addEventListener('input', updatePreview);
    priceInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    imageInput.addEventListener('input', updatePreview);
});
</script>
@endpush

