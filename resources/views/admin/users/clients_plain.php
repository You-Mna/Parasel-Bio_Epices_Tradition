<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>Admin - Parasel-Bio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/app.css?v=<?= time() ?>">
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
            <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <nav class="nav">
                <a href="/admin" class="<?= request()->path() === 'admin' ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-line"></i> Tableau de bord
                </a>
                <a href="/admin/products" class="<?= request()->is('admin/products*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-box"></i> Produits
                </a>
                <a href="/admin/orders" class="<?= request()->is('admin/orders*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-shopping-bag"></i> Commandes
                </a>
                <a href="/admin/shipments" class="<?= request()->is('admin/shipments*') || request()->is('admin/points-de-vente*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-truck"></i> Expéditions
                </a>
                <?php
                $isRelationActive = request()->is('admin/users*') || request()->is('admin/messages*') || request()->is('admin/experiences*');
                ?>
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle <?= $isRelationActive ? 'active' : '' ?>">
                        <i class="fa-solid fa-users"></i> Relation clients
                    </a>
                    <div class="nav-dropdown-menu">
                        <a href="/admin/messages" class="nav-dropdown-item <?= request()->is('admin/messages*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-envelope"></i> Messages
                        </a>
                        <a href="/admin/experiences" class="nav-dropdown-item <?= request()->is('admin/experiences*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-star"></i> Expériences
                        </a>
                        <a href="/admin/users" class="nav-dropdown-item <?= request()->is('admin/users*') ? 'active' : '' ?>">
                            <i class="fa-solid fa-address-book"></i> Clients
                        </a>
                    </div>
                </div>
                <a href="/admin/reports" class="<?= request()->is('admin/reports*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-pie"></i> Rapports
                </a>
                <span class="nav-separator" aria-hidden="true"></span>
                <div class="nav-dropdown">
                    <a href="#" class="nav-dropdown-toggle" title="Mon espace admin" aria-label="Mon espace admin">
                        <i class="fa-solid fa-user-shield"></i>
                    </a>
                    <div class="nav-dropdown-menu">
                        <div class="nav-dropdown-header">
                            <div class="nav-user-info">
                                <div class="nav-user-avatar"><i class="fa-solid fa-user-shield"></i></div>
                                <div class="nav-user-details">
                                    <div class="nav-user-name">Admin Parasel</div>
                                    <div class="nav-user-role">Administrateur</div>
                                </div>
                            </div>
                        </div>
                        <div class="nav-dropdown-divider"></div>
                        <a href="/" class="nav-dropdown-item"><i class="fa-solid fa-home"></i> Boutique principale</a>
                        <div class="nav-dropdown-divider"></div>
                        <form method="POST" action="<?= e(route('logout')) ?>" class="nav-dropdown-form" id="logout-form">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                            <button type="submit" class="nav-dropdown-item nav-logout-item">
                                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="mobile-nav" id="mobile-nav">
                <div class="mobile-nav-content">
                    <a href="/admin" class="mobile-nav-item <?= request()->path() === 'admin' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Tableau de bord</a>
                    <a href="/admin/products" class="mobile-nav-item <?= request()->is('admin/products*') ? 'active' : '' ?>"><i class="fa-solid fa-box"></i> Produits</a>
                    <a href="/admin/orders" class="mobile-nav-item <?= request()->is('admin/orders*') ? 'active' : '' ?>"><i class="fa-solid fa-shopping-bag"></i> Commandes</a>
                    <a href="/admin/users" class="mobile-nav-item <?= request()->is('admin/users*') ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Clients</a>
                    <a href="/admin/messages" class="mobile-nav-item <?= request()->is('admin/messages*') ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i> Messages</a>
                    <a href="/admin/experiences" class="mobile-nav-item <?= request()->is('admin/experiences*') ? 'active' : '' ?>"><i class="fa-solid fa-star"></i> Expériences</a>
                    <a href="/admin/shipments" class="mobile-nav-item <?= request()->is('admin/shipments*') || request()->is('admin/points-de-vente*') ? 'active' : '' ?>"><i class="fa-solid fa-truck"></i> Expéditions</a>
                    <a href="/admin/reports" class="mobile-nav-item <?= request()->is('admin/reports*') ? 'active' : '' ?>"><i class="fa-solid fa-chart-pie"></i> Rapports</a>
                    <div class="mobile-nav-divider"></div>
                    <div class="mobile-nav-user">
                        <div class="mobile-nav-user-info">
                            <div class="mobile-nav-user-avatar"><i class="fa-solid fa-user-shield"></i></div>
                            <div class="mobile-nav-user-details"><h4>Admin Parasel</h4><p>Administrateur</p></div>
                        </div>
                        <div class="mobile-nav-user-actions"><a href="/"><i class="fa-solid fa-home"></i> Boutique principale</a></div>
                        <div class="mobile-nav-logout">
                            <form method="POST" action="<?= e(route('logout')) ?>" id="mobile-logout-form">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                                <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container main-content">
        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Carnet d'adresses clients</h1>
                <p class="admin-page-subtitle">Liste des comptes clients</p>
            </div>
        </div>

        <?php if ($users->count() > 0): ?>
        <div class="admin-table-wrapper admin-table-wrapper--users">
            <table class="admin-table admin-table--users">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-nom">Nom</th>
                        <th class="col-prenom">Prénom</th>
                        <th class="col-email">Email</th>
                        <th class="col-phone">Téléphone</th>
                        <th class="col-orders">Nombre de commandes</th>
                        <th class="col-date">Date de création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td data-label="ID" class="col-id">#<?= (int) $user->id ?></td>
                        <td data-label="Nom" class="col-nom"><?= htmlspecialchars($user->last_name ?? '-') ?></td>
                        <td data-label="Prénom" class="col-prenom"><?= htmlspecialchars($user->first_name ?? '-') ?></td>
                        <td data-label="Email" class="col-email"><?= htmlspecialchars($user->email) ?></td>
                        <td data-label="Téléphone" class="col-phone"><?= htmlspecialchars($user->phone ?? '-') ?></td>
                        <td data-label="Nombre de commandes" class="col-orders"><?= (int) $user->orders_count ?></td>
                        <td data-label="Créé le" class="col-date"><?= $user->created_at->format('d/m/Y H:i') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="admin-table-pagination">
            <?php if ($users->hasPages()): ?>
                <p>
                    <?php if ($users->onFirstPage()): ?>
                        <span>Précédent</span>
                    <?php else: ?>
                        <a href="<?= e($users->previousPageUrl()) ?>">Précédent</a>
                    <?php endif; ?>
                    &nbsp;| Page <?= $users->currentPage() ?> / <?= $users->lastPage() ?> |&nbsp;
                    <?php if ($users->hasMorePages()): ?>
                        <a href="<?= e($users->nextPageUrl()) ?>">Suivant</a>
                    <?php else: ?>
                        <span>Suivant</span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="card" style="text-align:center;padding:40px;">
            <h3>Aucun client</h3>
            <p>Aucun compte client n'a encore été créé.</p>
        </div>
        <?php endif; ?>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function positionDropdownMenu(trigger, menu) {
                if (!trigger || !menu) return;
                var rect = trigger.getBoundingClientRect();
                var top = rect.bottom + 6;
                var maxTop = window.innerHeight - menu.offsetHeight - 8;
                if (top > maxTop) top = Math.max(8, maxTop);
                var left = rect.left + (rect.width / 4);
                var maxLeft = window.innerWidth - menu.offsetWidth - 8;
                if (left > maxLeft) left = Math.max(8, maxLeft);
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
                        m.style.top = ''; m.style.left = ''; m.style.right = '';
                    }
                });
            }
            dropdowns.forEach(function(dropdown) {
                var trigger = dropdown.querySelector('.nav-dropdown-toggle');
                var menu = dropdown.querySelector('.nav-dropdown-menu');
                if (!trigger || !menu) return;
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (menu.classList.contains('show-fixed')) {
                        menu.classList.remove('show-fixed');
                        menu.style.top = ''; menu.style.left = ''; menu.style.right = '';
                    } else {
                        closeAllDropdowns(menu);
                        positionDropdownMenu(trigger, menu);
                        menu.classList.add('show-fixed');
                    }
                });
            });
            document.addEventListener('click', function() { closeAllDropdowns(null); });
            window.addEventListener('resize', function() {
                dropdowns.forEach(function(dropdown) {
                    var trigger = dropdown.querySelector('.nav-dropdown-toggle');
                    var menu = dropdown.querySelector('.nav-dropdown-menu');
                    if (trigger && menu && menu.classList.contains('show-fixed')) positionDropdownMenu(trigger, menu);
                });
            });
            var mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            var mobileNav = document.getElementById('mobile-nav');
            if (mobileMenuToggle && mobileNav) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileNav.classList.toggle('show');
                    mobileMenuToggle.classList.toggle('active');
                });
                document.addEventListener('click', function(event) {
                    if (!mobileMenuToggle.contains(event.target) && !mobileNav.contains(event.target)) {
                        mobileNav.classList.remove('show');
                        mobileMenuToggle.classList.remove('active');
                    }
                });
                mobileNav.querySelectorAll('.mobile-nav-item').forEach(function(item) {
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
