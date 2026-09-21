<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Parasel-Bio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        $cssPath = public_path('css/app.css');
        $cssV = is_file($cssPath) ? (string) filemtime($cssPath) : '1';
    @endphp
    <link rel="stylesheet" href="{{ url('/css/app.css') }}?v={{ $cssV }}">
</head>
<body class="admin-layout">
    <header class="site-header">
        <div class="container flex between center">
            <a href="/" class="brand">
                <div class="brand-logo-text">
                    <div class="logo-main">
                        <span class="logo-parasel">
                            <span class="logo-para">Para</span>
                            <span class="logo-s">s</span>
                            <span class="logo-el">el</span>
                            <span class="logo-reg">®</span>
                        </span>
                        <div class="logo-bio">
                            <span class="bio-line"></span>
                            <span class="bio-text">BIO</span>
                            <span class="bio-line"></span>
                        </div>
                        <div class="logo-tagline">Sale & Épice ta Vie.</div>
                    </div>
                </div>
            </a>
            <!-- Menu Hamburger pour Mobile -->
            <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            
            <!-- Navigation Desktop -->
            <nav class="nav">
                <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Tableau de bord
                </a>
                <a href="/admin/products" class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box"></i> Produits
                </a>
                <a href="/admin/orders" class="{{ request()->is('admin/orders*') ? 'active' : '' }}">
                    <i class="fa-solid fa-shopping-bag"></i> Commandes
                </a>
                <a href="/admin/shipments" class="{{ request()->is('admin/shipments*') || request()->is('admin/points-de-vente*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck"></i> Expéditions
                </a>

                {{-- Menu déroulant Relation clients : Clients, Messages, Expériences --}}
                @php
                    $isRelationActive = request()->is('admin/users*')
                        || request()->is('admin/messages*')
                        || request()->is('admin/experiences*');
                @endphp
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle {{ $isRelationActive ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i> Relation clients
                    </a>
                    <div class="nav-dropdown-menu">
                        <a href="/admin/messages" class="nav-dropdown-item {{ request()->is('admin/messages*') ? 'active' : '' }}">
                            <i class="fa-solid fa-envelope"></i> Messages
                        </a>
                        <a href="/admin/experiences" class="nav-dropdown-item {{ request()->is('admin/experiences*') ? 'active' : '' }}">
                            <i class="fa-solid fa-star"></i> Expériences
                        </a>
                        <a href="/admin/users" class="nav-dropdown-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="fa-solid fa-address-book"></i> Clients
                        </a>
                    </div>
                </div>
                <a href="/admin/reports" class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Rapports
                </a>
                <span class="nav-separator" aria-hidden="true"></span>
                
                <!-- Dropdown Admin -->
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle" title="Mon espace admin" aria-label="Mon espace admin">
                        <i class="fa-solid fa-user-shield"></i>
                    </a>
                    <div class="nav-dropdown-menu">
                        <div class="nav-dropdown-header">
                            <div class="nav-user-info">
                                <div class="nav-user-avatar">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <div class="nav-user-details">
                                    <div class="nav-user-name">Admin Parasel</div>
                                    <div class="nav-user-role">Administrateur</div>
                                </div>
                            </div>
                        </div>
                        <div class="nav-dropdown-divider"></div>
                        <a href="/" class="nav-dropdown-item">
                            <i class="fa-solid fa-home"></i> Boutique principale
                        </a>
                        <div class="nav-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="nav-dropdown-form" id="logout-form">
                            @csrf
                            <button type="submit" class="nav-dropdown-item nav-logout-item">
                                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
            
            <!-- Navigation Mobile -->
            <div class="mobile-nav" id="mobile-nav">
                <div class="mobile-nav-content">
                    <a href="/admin" class="mobile-nav-item {{ request()->is('admin') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i> Tableau de bord
                    </a>
                    <a href="/admin/products" class="mobile-nav-item {{ request()->is('admin/products*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box"></i> Produits
                    </a>
                    <a href="/admin/orders" class="mobile-nav-item {{ request()->is('admin/orders*') ? 'active' : '' }}">
                        <i class="fa-solid fa-shopping-bag"></i> Commandes
                    </a>
                    <a href="/admin/users" class="mobile-nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i> Clients
                    </a>
                    <a href="/admin/messages" class="mobile-nav-item {{ request()->is('admin/messages*') ? 'active' : '' }}">
                        <i class="fa-solid fa-envelope"></i> Messages
                    </a>
                    <a href="/admin/experiences" class="mobile-nav-item {{ request()->is('admin/experiences*') ? 'active' : '' }}">
                        <i class="fa-solid fa-star"></i> Expériences
                    </a>
                    <a href="/admin/shipments" class="mobile-nav-item {{ request()->is('admin/shipments*') || request()->is('admin/points-de-vente*') ? 'active' : '' }}">
                        <i class="fa-solid fa-truck"></i> Expéditions
                    </a>
                    <a href="/admin/reports" class="mobile-nav-item {{ request()->is('admin/reports*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i> Rapports
                    </a>
                    
                    <div class="mobile-nav-divider"></div>
                    <div class="mobile-nav-user">
                        <div class="mobile-nav-user-info">
                            <div class="mobile-nav-user-avatar">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <div class="mobile-nav-user-details">
                                <h4>Admin Parasel</h4>
                                <p>Administrateur</p>
                            </div>
                        </div>
                        
                        <div class="mobile-nav-user-actions">
                            <a href="/">
                                <i class="fa-solid fa-home"></i> Boutique principale
                            </a>
                        </div>
                        
                        <div class="mobile-nav-logout">
                            <form method="POST" action="{{ route('logout') }}" id="mobile-logout-form">
                                @csrf
                                <button type="submit">
                                    <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="container main-content">
        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error">
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif
        @yield('content')
    </main>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">
                        <span class="footer-logo-parasel">
                            <span class="logo-para">Para</span>
                            <span class="logo-s">s</span>
                            <span class="logo-el">el</span>
                        </span>-<span class="footer-bio">BIO</span> <span class="footer-epices">Épices et Tradition</span>
                    </h3>
                    <p class="footer-tagline">Assaisonnement aux 17 épices et légumes naturels bio</p>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Certifications & Autorisations</h4>
                    <div class="certification-item">
                        <span class="cert-label">Aut. N°:</span>
                        <span class="cert-value">440/2014/FRA-Sénégal</span>
                    </div>
                    <div class="certification-item">
                        <span class="cert-label">Certifié N°:</span>
                        <span class="cert-value">02/ANM/2017-Bénin</span>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Entreprise</h4>
                    <p class="company-name">Soum Multime Dia & Co</p>
                    <div class="company-details">
                        <span class="detail-label">Service consommateur</span>
                        <span class="detail-value">IFU: N° 1201643510102</span>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Distinction</h4>
                    <div class="award">
                        <span class="award-text">Distingué F™™ Prix d'Innovation 2017</span>
                        <span class="award-category">Agroalimentaire & Santé</span>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="copyright">&copy; {{ date('Y') }} Parasel-Bio. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <script>
        function attachSubmitLoaders(selector) {
            var forms = document.querySelectorAll(selector);
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    if (form.hasAttribute('data-manual-loader')) {
                        return;
                    }
                    if (form.dataset.submitting === '1') {
                        return;
                    }
                    form.dataset.submitting = '1';

                    var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitButtons.forEach(function(btn) {
                        btn.disabled = true;
                        btn.classList.add('is-loading');

                        var loadingText = btn.getAttribute('data-loading-text') || 'Traitement...';
                        if (!btn.dataset.originalHtml) {
                            btn.dataset.originalHtml = btn.innerHTML;
                        }

                        if (btn.tagName === 'INPUT') {
                            btn.value = loadingText;
                            return;
                        }

                        btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span><span>' + loadingText + '</span>';
                    });
                });
            });
        }

        // Gestion de la déconnexion avec confirmation élégante
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForms = document.querySelectorAll('#logout-form');
            
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showLogoutConfirmation(form);
                });
            });

            attachSubmitLoaders('form[method="POST"]:not(#logout-form):not(#mobile-logout-form)');
        });

        function showLogoutConfirmation(form) {
            // Créer le toast de confirmation
            const toast = document.createElement('div');
            toast.className = 'confirmation-toast';
            toast.innerHTML = `
                <div class="confirmation-content">
                    <div class="confirmation-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16,17 21,12 16,7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </div>
                    <div class="confirmation-text">
                        <div class="confirmation-message">Voulez-vous vraiment vous déconnecter ?</div>
                    </div>
                    <div class="confirmation-actions">
                        <button class="btn-confirm" onclick="confirmLogout(true, this)">Oui</button>
                        <button class="btn-cancel" onclick="confirmLogout(false)">Non</button>
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
                color: #f59e0b;
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
                background: #f59e0b;
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
            window.logoutToast = toast;
            window.logoutOverlay = overlay;
            window.logoutForm = form;
        }

        function showLogoutLoader(text) {
            if (document.getElementById('logout-submit-loader')) return;
            var overlay = document.createElement('div');
            overlay.id = 'logout-submit-loader';
            overlay.style.cssText =
                'position:fixed;inset:0;background:rgba(17,24,39,0.45);z-index:20000;display:flex;align-items:center;justify-content:center;padding:16px;';
            overlay.innerHTML =
                '<div style="background:#fff;border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:10px;max-width:92vw;">' +
                    '<span class="btn-spinner" aria-hidden="true"></span>' +
                    '<span style="font-weight:600;color:#111827;">' + text + '</span>' +
                '</div>';
            document.body.appendChild(overlay);
        }

        function confirmLogout(confirmed, confirmBtn) {
            if (confirmed && window.logoutForm) {
                if (confirmBtn) {
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('is-loading');
                    confirmBtn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span><span>Déconnexion...</span>';
                }
                showLogoutLoader('Déconnexion en cours...');

                // Créer un nouveau token CSRF si nécessaire
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const csrfInput = window.logoutForm.querySelector('input[name="_token"]');
                    if (csrfInput) {
                        csrfInput.value = csrfToken.getAttribute('content');
                    }
                }
                
                // Soumettre le formulaire
                window.logoutForm.submit();
            }
            
            // Supprimer le toast et l'overlay
            if (window.logoutToast) {
                window.logoutToast.remove();
            }
            if (window.logoutOverlay) {
                window.logoutOverlay.remove();
            }
        }

        // Positionner les dropdowns (Relation clients + Admin) en fixed, ouverture au clic
        document.addEventListener('DOMContentLoaded', function() {
            function positionDropdownMenu(trigger, menu) {
                if (!trigger || !menu) return;
                var rect = trigger.getBoundingClientRect();

                // Position verticale : juste en dessous du bouton
                var top = rect.bottom + 6;
                // Empêcher de sortir de l'écran en bas
                var maxTop = window.innerHeight - menu.offsetHeight - 8;
                if (top > maxTop) {
                    top = Math.max(8, maxTop);
                }

                // Position horizontale : alignée sous le bouton, centrée visuellement sous « Relation clients »
                var left = rect.left + (rect.width / 4);
                var maxLeft = window.innerWidth - menu.offsetWidth - 8;
                if (left > maxLeft) {
                    left = Math.max(8, maxLeft);
                }

                menu.style.top = top + 'px';
                menu.style.left = left + 'px';
                menu.style.right = 'auto';
            }

            var dropdowns = document.querySelectorAll('.admin-layout .nav-dropdown');

            function closeAllDropdowns(exceptMenu) {
                dropdowns.forEach(function(dropdown) {
                    var m = dropdown.querySelector('.nav-dropdown-menu');
                    if (m && m !== exceptMenu) {
                        m.classList.remove('show-fixed');
                        m.style.top = '';
                        m.style.left = '';
                        m.style.right = '';
                    }
                });
            }

            dropdowns.forEach(function(dropdown) {
                var trigger = dropdown.querySelector('.nav-dropdown-toggle');
                var menu = dropdown.querySelector('.nav-dropdown-menu');
                if (!trigger || !menu) return;

                trigger.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (menu.classList.contains('show-fixed')) {
                        // Fermer si déjà ouvert
                        menu.classList.remove('show-fixed');
                        menu.style.top = '';
                        menu.style.left = '';
                        menu.style.right = '';
                    } else {
                        // Fermer les autres et ouvrir celui-ci
                        closeAllDropdowns(menu);
                        positionDropdownMenu(trigger, menu);
                        menu.classList.add('show-fixed');
                    }
                });
            });

            // Fermer en cliquant en dehors
            document.addEventListener('click', function() {
                closeAllDropdowns(null);
            });

            // Repositionner si on redimensionne la fenêtre
            window.addEventListener('resize', function() {
                dropdowns.forEach(function(dropdown) {
                    var trigger = dropdown.querySelector('.nav-dropdown-toggle');
                    var menu = dropdown.querySelector('.nav-dropdown-menu');
                    if (!trigger || !menu) return;
                    if (menu.classList.contains('show-fixed')) {
                        positionDropdownMenu(trigger, menu);
                    }
                });
            });
        });

        // Menu Hamburger Mobile
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileNav = document.getElementById('mobile-nav');
            
            if (mobileMenuToggle && mobileNav) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileNav.classList.toggle('show');
                    mobileMenuToggle.classList.toggle('active');
                });
                
                // Fermer le menu en cliquant à l'extérieur
                document.addEventListener('click', function(event) {
                    if (!mobileMenuToggle.contains(event.target) && !mobileNav.contains(event.target)) {
                        mobileNav.classList.remove('show');
                        mobileMenuToggle.classList.remove('active');
                    }
                });
                
                // Fermer le menu en cliquant sur un lien
                const mobileNavItems = mobileNav.querySelectorAll('.mobile-nav-item');
                mobileNavItems.forEach(function(item) {
                    item.addEventListener('click', function() {
                        mobileNav.classList.remove('show');
                        mobileMenuToggle.classList.remove('active');
                    });
                });
            }
        });
    </script>
</body>
</html> 