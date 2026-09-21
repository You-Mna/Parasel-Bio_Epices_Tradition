@extends('layouts.app')

@section('content')
<div class="auth-page">
<div class="card form-narrow centered-block">
    <h2>Inscription</h2>
    <form method="POST" action="{{ route('register') }}" class="form-grid cols-2" id="register-form" data-manual-loader>
        @csrf
        <div>
            <label>Prénom <span style="color: #dc2626;">*</span></label>
            <input name="first_name" required>
        </div>
        <div>
            <label>Nom <span style="color: #dc2626;">*</span></label>
            <input name="last_name" required>
        </div>
        <div class="full">
            <label>Email <span style="color: #dc2626;">*</span></label>
            <input name="email" type="email" required>
        </div>
        <div>
            <label>Téléphone <span style="color: #dc2626;">*</span></label>
            <input name="phone" type="tel" pattern="[0-9]+" inputmode="numeric" required>
        </div>
        <div>
            <label>Mot de passe <span style="color: #dc2626;">*</span></label>
            <div class="password-input-container">
                <input name="password" type="password" id="password" required>
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                    <svg id="toggle-icon-password" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
        </div>
        <div class="full">
            <label>Confirmer le mot de passe <span style="color: #dc2626;">*</span></label>
            <div class="password-input-container">
                <input name="password_confirmation" type="password" id="password_confirmation" required>
                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                    <svg id="toggle-icon-password_confirmation" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
        </div>
        <div class="actions full"><button class="btn" data-loading-text="Création du compte...">Créer mon compte</button></div>
    </form>
    <p style="margin-top:12px;color:#6b7280;">
        Déjà un compte ?
        <a href="{{ route('login.show') }}" style="color:#1a8f5c;text-decoration:underline;">Se connecter</a>
    </p>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var registerForm = document.getElementById('register-form');
    var registerButton = registerForm ? registerForm.querySelector('button[type="submit"], .btn') : null;

    function showAuthOverlay(text) {
        if (document.getElementById('auth-submit-loader')) return;
        var overlay = document.createElement('div');
        overlay.id = 'auth-submit-loader';
        overlay.style.cssText =
            'position:fixed;inset:0;background:rgba(17,24,39,0.45);z-index:20000;display:flex;align-items:center;justify-content:center;padding:16px;';
        overlay.innerHTML =
            '<div style="background:#fff;border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:10px;max-width:92vw;">' +
                '<span class="btn-spinner" aria-hidden="true"></span>' +
                '<span style="font-weight:600;color:#111827;">' + text + '</span>' +
            '</div>';
        document.body.appendChild(overlay);
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            if (registerForm.dataset.submitting === '1') return;
            registerForm.dataset.submitting = '1';
            if (registerButton) {
                registerButton.disabled = true;
                registerButton.classList.add('is-loading');
                registerButton.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span><span>Création du compte...</span>';
            }
            showAuthOverlay('Création du compte...');
        });
    }

    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById('toggle-icon-' + inputId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            `;
        } else {
            passwordInput.type = 'password';
            toggleIcon.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            `;
        }
    }
    
    // Rendre la fonction globale
    window.togglePassword = togglePassword;
});
</script>
@endpush

