@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Témoignages/Retours</h1>
        <p class="admin-page-subtitle">Gérez les témoignages et retours d'expérience de vos clients</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('home') }}" target="_blank" class="btn-admin secondary">
            <i class="fas fa-external-link-alt"></i> Voir l'accueil
        </a>
    </div>
</div>
<form method="POST" action="{{ route('admin.experiences.store') }}" class="card">
    @csrf
    <label>Nom de l’auteur</label><input name="author_name" required>
    <label>Contenu</label><textarea name="content" required></textarea>
    <label><input type="checkbox" name="is_published" value="1"> Publier</label>
    <button class="btn" style="margin-top:10px;">Ajouter</button>
 </form>

<table style="margin-top:16px;">
    <thead><tr><th>Auteur</th><th>Contenu</th><th>Publié</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($experiences as $e)
        <tr>
            <td>{{ $e->author_name }}</td>
            <td>{{ $e->content }}</td>
            <td>{{ $e->is_published ? 'Oui' : 'Non' }}</td>
            <td class="flex" style="gap:8px;">
                <a class="btn secondary" href="{{ route('admin.experiences.edit', $e) }}">Modifier</a>
                <button class="btn secondary" onclick="showDeleteConfirmation('{{ $e->id }}', '{{ addslashes($e->author_name) }}')">Supprimer</button>
            </td>
        </tr>
    @endforeach
    </tbody>
 </table>
 <div style="margin-top:16px;">{{ $experiences->links() }}</div>

<script>
function showDeleteConfirmation(experienceId, authorName) {
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
                <div class="confirmation-message">Voulez-vous supprimer le témoignage de "${authorName}" ?</div>
            </div>
            <div class="confirmation-actions">
                <button class="btn-confirm" onclick="confirmDelete(true, ${experienceId})">Oui</button>
                <button class="btn-cancel" onclick="confirmDelete(false, ${experienceId})">Non</button>
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
    
    const btnConfirm = toast.querySelector('.btn-confirm');
    btnConfirm.style.cssText = `
        background: #ef4444;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
    `;
    
    const btnCancel = toast.querySelector('.btn-cancel');
    btnCancel.style.cssText = `
        background: #f3f4f6;
        color: #374151;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
    `;
    
    // Ajouter au DOM
    document.body.appendChild(toast);
    
    // Ajouter un overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    `;
    document.body.appendChild(overlay);
    
    // Stocker les références
    window.deleteToast = toast;
    window.deleteOverlay = overlay;
}

function confirmDelete(confirmed, experienceId) {
    // Supprimer le toast et l'overlay
    if (window.deleteToast) {
        window.deleteToast.remove();
    }
    if (window.deleteOverlay) {
        window.deleteOverlay.remove();
    }
    
    if (confirmed) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/experiences/${experienceId}`;
        
        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        // Ajouter la méthode DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

