@extends('layouts.app')

@section('content')
<div class="auth-page">
<div class="card form-narrow centered-block">
    <h2>Connexion</h2>
    
    @if(session('message'))
        <div class="alert alert-info" style="background-color: #e3f2fd; color: #1976d2; padding: 12px; border-radius: 6px; margin-bottom: 16px; border-left: 4px solid #1976d2;">
            <i class="fa-solid fa-info-circle" style="margin-right: 8px;"></i>
            {{ session('message') }}
        </div>
    @endif
    <form method="POST" action="{{ route('login') }}" class="form-grid">
        @csrf
        <label>Email</label>
        <input name="email" type="email" required>
        <label>Mot de passe</label>
        <div class="password-input-container">
            <input name="password" type="password" id="password" required>
            <button type="button" class="password-toggle" onclick="togglePassword()">
                <svg id="toggle-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
        </div>
        <div class="actions full"><button class="btn">Se connecter</button></div>
    </form>
    <p style="margin-top:12px;color:#6b7280;">
        Vous n'avez pas encore de compte ?
        <a href="{{ route('register.show') }}" style="color:#1a8f5c;text-decoration:underline;">Créer un compte</a>
    </p>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggle-icon');
        
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

