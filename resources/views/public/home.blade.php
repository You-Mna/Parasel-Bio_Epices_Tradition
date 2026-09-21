@extends('layouts.app')

@section('content')
<div class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">
            <span class="hero-title-typing">
                <span class="logo-parasel">
                    <span class="logo-para">Para</span>
                    <span class="logo-s">s</span>
                    <span class="logo-el">el</span>
                </span>-<span class="bio-text">BIO</span> <span class="logo-epices">Épices Tradition</span>
            </span>
        </h1>
        <p class="hero-subtitle">Assaisonnement aux 17 épices et légumes naturels bio</p>
        <p class="hero-description">Découvrez l'authenticité des épices africaines bio, sélectionnées avec soin pour allier saveur, santé et bien-être au quotidien.</p>
        <div class="hero-actions">
            <a href="/produits" class="btn btn-primary">Découvrir nos produits</a>
            <a href="/contact" class="btn btn-secondary">Nous contacter</a>
        </div>
    </div>
</div>

<div class="company-section">
    <div class="section-header">
        <h2><span class="section-title-text">Notre entreprise</span></h2>
        <p>Une référence dans les assaisonnements naturels et bio</p>
    </div>

    <div class="company-showcase" id="company-showcase">
        <div class="company-showcase__main">
            {{-- Bloc 1 : colonne gauche, style « image 1 » (icône + titre + texte) --}}
            <section class="company-showcase__column company-showcase__column--left" aria-label="Notre identité">
                <div class="company-detail">
                    <div class="company-detail__icon"><i class="fa-solid fa-leaf" aria-hidden="true"></i></div>
                    <div class="company-detail__body">
                        <h3 class="company-detail__title">100 % naturel</h3>
                        <p class="company-detail__desc">Parasel-Bio est une entreprise béninoise spécialisée dans la production d'assaisonnements 100 % naturels.</p>
                    </div>
                </div>
                <div class="company-detail">
                    <div class="company-detail__icon"><i class="fa-solid fa-mortar-pestle" aria-hidden="true"></i></div>
                    <div class="company-detail__body">
                        <h3 class="company-detail__title">Sel marin &amp; épices</h3>
                        <p class="company-detail__desc">Son produit phare associe 17 épices et légumes locaux à un sel iodé récolté traditionnellement.</p>
                    </div>
                </div>
                <div class="company-detail">
                    <div class="company-detail__icon"><i class="fa-solid fa-heart-pulse" aria-hidden="true"></i></div>
                    <div class="company-detail__body">
                        <h3 class="company-detail__title">Héritage certifié bio</h3>
                        <p class="company-detail__desc">Une combinaison unique entre héritage culinaire africain et procédés modernes certifiés bio.</p>
                    </div>
                </div>
            </section>

            {{-- Centre : mot-logo (wrapper invisible pour les animations, pas de carte) --}}
            <div class="company-showcase__column company-showcase__column--center">
                <div class="company-showcase__center-brand">
                    <div class="company-brand-wordmark" role="group" aria-label="Parasel BIO Épices Tradition">
                        <p class="company-brand-wordmark__name">
                            <span class="company-brand-wordmark__ink">Para</span><span class="company-brand-wordmark__accent">s</span><span class="company-brand-wordmark__ink">el</span>
                        </p>
                        <p class="company-brand-wordmark__bio-row">
                            <span class="company-brand-wordmark__bio-line" aria-hidden="true"></span>
                            <span class="company-brand-wordmark__bio">BIO</span>
                            <span class="company-brand-wordmark__bio-line" aria-hidden="true"></span>
                        </p>
                        <p class="company-brand-wordmark__tagline">Épices Tradition</p>
                    </div>
                </div>
            </div>

            {{-- Bloc 3 : colonne droite, style « image 1 » --}}
            <section class="company-showcase__column company-showcase__column--right" aria-label="Notre vision">
                <div class="company-detail">
                    <div class="company-detail__icon"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i></div>
                    <div class="company-detail__body">
                        <h3 class="company-detail__title">Producteurs locaux</h3>
                        <p class="company-detail__desc">Parasel-Bio valorise les ressources agricoles du Bénin et soutient les producteurs locaux.</p>
                    </div>
                </div>
                <div class="company-detail">
                    <div class="company-detail__icon"><i class="fa-solid fa-earth-americas" aria-hidden="true"></i></div>
                    <div class="company-detail__body">
                        <h3 class="company-detail__title">Rayon international</h3>
                        <p class="company-detail__desc">Nos assaisonnements sont déjà présents sur plusieurs marchés internationaux.</p>
                    </div>
                </div>
                <div class="company-detail">
                    <div class="company-detail__icon"><i class="fa-solid fa-bullseye" aria-hidden="true"></i></div>
                    <div class="company-detail__body">
                        <h3 class="company-detail__title">Vision mondiale</h3>
                        <p class="company-detail__desc">Faire de Parasel-Bio une référence mondiale des assaisonnements naturels.</p>
                    </div>
                </div>
            </section>
        </div>

        {{-- Bloc 2 : bandeau du bas, style « image 2 » (icône au-dessus, titre, sous-texte) --}}
        <section class="company-showcase__bottom" aria-label="Notre engagement">
            <div class="company-stripe">
                <div class="company-stripe__icon"><i class="fa-solid fa-flask" aria-hidden="true"></i></div>
                <h3 class="company-stripe__title">Laboratoire &amp; normes</h3>
                <p class="company-stripe__subtitle">Les produits Parasel-Bio sont rigoureusement analysés en laboratoire et conformes aux normes d'hygiène et de sécurité.</p>
            </div>
            <div class="company-stripe">
                <div class="company-stripe__icon"><i class="fa-solid fa-award" aria-hidden="true"></i></div>
                <h3 class="company-stripe__title">Prix innovation 2017</h3>
                <p class="company-stripe__subtitle">En 2017, l'entreprise a reçu le Prix TIB/MESRS en Innovation Agroalimentaire et Santé, reconnaissance de son sérieux et de son expertise.</p>
            </div>
            <div class="company-stripe">
                <div class="company-stripe__icon"><i class="fa-solid fa-ban" aria-hidden="true"></i></div>
                <h3 class="company-stripe__title">Sans additifs chimiques</h3>
                <p class="company-stripe__subtitle">Chaque formulation répond aux attentes d'une clientèle exigeante, avec des assaisonnements sans additifs chimiques, alliant goût et équilibre nutritionnel.</p>
            </div>
        </section>
    </div>
</div>

{{-- Vitrine boutique : texte à gauche + défilement horizontal (4 produits, le 4ᵉ entrevu) --}}
<section class="home-products-showcase" id="home-products-showcase" aria-labelledby="home-products-intro-title">
    <div class="home-products-showcase__inner">
        @if($homeProducts->isEmpty())
            <header class="home-products-showcase__intro home-products-showcase__intro--solo">
                <h2 id="home-products-intro-title" class="home-products-showcase__intro-title">
                    <span class="home-products-showcase__intro-title-line">Nos </span><span class="home-products-showcase__intro-title-accent">assaisonnements</span>
                </h2>
                <p class="home-products-showcase__intro-text">
                    Nos ingrédients et nos produits sont choisis avec soin pour garantir des assaisonnements naturels et de qualité.
                    <a href="{{ route('products.index') }}" class="home-products-showcase__intro-link">En savoir plus !</a>
                </p>
            </header>
            <p class="home-products-showcase__empty">Aucun produit disponible pour le moment.</p>
        @else
            <div class="home-products-showcase__layout">
                <header class="home-products-showcase__intro">
                    <h2 id="home-products-intro-title" class="home-products-showcase__intro-title">
                        <span class="home-products-showcase__intro-title-line">Nos </span><span class="home-products-showcase__intro-title-accent">assaisonnements</span>
                    </h2>
                    <p class="home-products-showcase__intro-text">
                        Nos ingrédients et nos produits sont choisis avec soin pour garantir des assaisonnements naturels et de qualité.
                        <a href="{{ route('products.index') }}" class="home-products-showcase__intro-link">En savoir plus !</a>
                    </p>
                </header>
                <div class="home-products-showcase__carousel" data-home-products-carousel>
                    <div class="home-products-showcase__scroll" tabindex="0" role="region" aria-label="Défiler pour voir les produits">
                        @foreach($homeProducts as $product)
                            <a
                                href="{{ route('products.index') }}#product-{{ $product->id }}"
                                class="home-product-card"
                            >
                                <div class="home-product-card__media">
                                    @include('public.partials.product-square-media', ['product' => $product])
                                </div>
                                <div class="home-product-card__body">
                                    <h3 class="home-product-card__name">{{ $product->name }}</h3>
                                    <p class="home-product-card__price">
                                        <span class="home-product-card__price-value">{{ number_format($product->displayMinPrice(), 0, ',', ' ') }} FCFA</span>
                                    </p>
                                    <span class="home-product-card__buy-btn" aria-hidden="true">Acheter</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Points de distribution : bandeau photo + disque (détail des adresses : page /points-de-vente, hors menu) --}}
@php
    $pdvHeroPath = public_path('images/points-distribution-hero.png');
    $pdvHeroV = is_file($pdvHeroPath) ? (int) filemtime($pdvHeroPath) : 1;
@endphp
<section class="distribution-hero" aria-labelledby="distribution-hero-title">
    <div class="distribution-hero__media"
        style="background-image: url('{{ asset('images/points-distribution-hero.png') }}?v={{ $pdvHeroV }}');"
        role="img"
        aria-hidden="true"></div>
    <div class="distribution-hero__tint" aria-hidden="true"></div>
    <div class="distribution-hero__shell">
        <div class="distribution-hero__circle">
            <div class="distribution-hero__badge" aria-hidden="true">
                <span class="distribution-hero__badge-icon"><i class="fa-solid fa-store"></i></span>
            </div>
            <h2 id="distribution-hero-title" class="distribution-hero__title">Points de Distribution</h2>
            <p class="distribution-hero__text">Nos produits sont disponibles au Bénin et à l’international. Trouvez le point de vente le plus proche.</p>
            <a href="{{ route('points.distribution') }}" class="btn btn-primary distribution-hero__cta">Voir les points de vente</a>
        </div>
    </div>
</section>

<div class="offline-order-cta offline-order-cta--below-distribution">
    <a class="btn btn-primary distribution-hero__cta offline-order-btn offline-order-btn--home-cta" href="/contact">
        <span class="offline-order-btn-text">En savoir plus</span>
    </a>
</div>

<div class="tips-section">
    <div class="section-header">
        <h2><span class="section-title-text">Conseils Nutritionnels</span></h2>
        <p>Adoptez une alimentation saine et équilibrée</p>
    </div>
    
    <div class="tips-grid tips-grid--blog" role="region" aria-label="Conseils nutritionnels">
        <article class="tip-card tip-card--blog collapsible">
            <div class="tip-card-media">
                {{-- Chemins racine (/images/...) : indépendants de APP_URL (évite images cassées en prod) --}}
                <img src="/images/conseils-nutrition/conseil-1.png" alt="Parasel-Bio, bon pour la santé" width="1000" height="667" loading="lazy" decoding="async" fetchpriority="low">
            </div>
            <div class="tip-card-body">
                <h3>Parasel-Bio, bon pour la santé</h3>
                <p class="tip-card-meta tip-card-meta-brand" aria-label="Parasel-Bio Épices Tradition">
                    <span class="tip-meta-parasel"><span class="logo-para">Para</span><span class="logo-s">s</span><span class="logo-el">el</span><span class="tip-meta-dash">-</span><span class="tip-meta-bio">Bio</span></span>
                    <span class="tip-meta-suite"><span class="tip-meta-epices">Épices</span> <span class="tip-meta-tradition">Tradition</span></span>
                </p>
                <div class="tip-card-excerpt">
                    <p class="tip-lede">Contrairement aux cubes chimiques riches en glutamate, exhausteurs de goût, sels raffinés et additifs artificiels, Parasel-Bio est élaboré à partir de 17 épices et légumes locaux associés à un sel marin iodé.</p>
                    <div class="collapsible-content">
                        <p>Ce mélange apporte non seulement une saveur authentique aux plats, mais aussi une véritable protection pour l'organisme :<br>
                        • Préserve le foie et les reins, souvent fragilisés par les excès d'additifs chimiques.<br>
                        • Contribue à réguler la tension artérielle grâce à son sel naturel iodé.<br>
                        • Renforce l'immunité grâce aux antioxydants présents dans les épices.</p>
                    </div>
                </div>
                <button type="button" class="collapsible-toggle tip-readmore-toggle" data-collapsible-toggle aria-expanded="false">Lire la suite</button>
            </div>
        </article>

        <article class="tip-card tip-card--blog collapsible">
            <div class="tip-card-media">
                <img src="/images/conseils-nutrition/conseil-2.png" alt="Les bienfaits des 17 épices" width="1024" height="684" loading="lazy" decoding="async" fetchpriority="low">
            </div>
            <div class="tip-card-body">
                <h3>Les bienfaits des 17 épices</h3>
                <p class="tip-card-meta tip-card-meta-brand" aria-label="Parasel-Bio Épices Tradition">
                    <span class="tip-meta-parasel"><span class="logo-para">Para</span><span class="logo-s">s</span><span class="logo-el">el</span><span class="tip-meta-dash">-</span><span class="tip-meta-bio">Bio</span></span>
                    <span class="tip-meta-suite"><span class="tip-meta-epices">Épices</span> <span class="tip-meta-tradition">Tradition</span></span>
                </p>
                <div class="tip-card-excerpt">
                    <p class="tip-lede">Chaque ingrédient de Parasel-Bio a été sélectionné pour soutenir un organe ou une fonction de l'organisme :</p>
                    <div class="collapsible-content">
                        <p>• Certains favorisent la digestion et apaisent les ballonnements.<br>
                        • D'autres stimulent la circulation sanguine et renforcent le cœur.<br>
                        • Plusieurs épices sont reconnues pour leurs propriétés anti-inflammatoires et antioxydantes, retardant le vieillissement cellulaire.<br><br>
                        Résultat : un assaisonnement qui ne se contente pas de relever les plats, mais qui agit comme un allié quotidien de votre bien-être.</p>
                    </div>
                </div>
                <button type="button" class="collapsible-toggle tip-readmore-toggle" data-collapsible-toggle aria-expanded="false">Lire la suite</button>
            </div>
        </article>

        <article class="tip-card tip-card--blog collapsible">
            <div class="tip-card-media">
                <img src="/images/conseils-nutrition/conseil-3.png" alt="Les dangers des additifs chimiques" width="1024" height="576" loading="lazy" decoding="async" fetchpriority="low">
            </div>
            <div class="tip-card-body">
                <h3>Les dangers des additifs chimiques</h3>
                <p class="tip-card-meta tip-card-meta-brand" aria-label="Parasel-Bio Épices Tradition">
                    <span class="tip-meta-parasel"><span class="logo-para">Para</span><span class="logo-s">s</span><span class="logo-el">el</span><span class="tip-meta-dash">-</span><span class="tip-meta-bio">Bio</span></span>
                    <span class="tip-meta-suite"><span class="tip-meta-epices">Épices</span> <span class="tip-meta-tradition">Tradition</span></span>
                </p>
                <div class="tip-card-excerpt">
                    <p class="tip-lede">Les bouillons industriels et assaisonnements chimiques sont souvent riches en glutamate monosodique (MSG), exhausteurs de goût, colorants artificiels et conservateurs.</p>
                    <div class="collapsible-content">
                        <p>Leur consommation excessive peut entraîner :<br>
                        • Hypertension et maladies cardiovasculaires dues à la forte teneur en sel raffiné.<br>
                        • Risques de cirrhose et d'atteintes hépatiques (cas réel à l'origine de la création de Parasel-Bio).<br>
                        • Puberté précoce chez les jeunes filles, avec l'apparition des menstrues dès l'âge de 8 ans.<br>
                        • Dépendance gustative : le palais s'habitue au goût artificiel et les plats naturels paraissent fades.<br><br>
                        Parasel-Bio est né pour offrir une alternative saine, sans danger pour la santé, respectueuse de l'organisme et de l'environnement.</p>
                    </div>
                </div>
                <button type="button" class="collapsible-toggle tip-readmore-toggle" data-collapsible-toggle aria-expanded="false">Lire la suite</button>
            </div>
        </article>

        <article class="tip-card tip-card--blog collapsible">
            <div class="tip-card-media">
                <img src="/images/conseils-nutrition/conseil-4.png" alt="Comment utiliser Parasel-Bio au quotidien ?" width="1024" height="682" loading="lazy" decoding="async" fetchpriority="low">
            </div>
            <div class="tip-card-body">
                <h3>Comment utiliser Parasel-Bio au quotidien ?</h3>
                <p class="tip-card-meta tip-card-meta-brand" aria-label="Parasel-Bio Épices Tradition">
                    <span class="tip-meta-parasel"><span class="logo-para">Para</span><span class="logo-s">s</span><span class="logo-el">el</span><span class="tip-meta-dash">-</span><span class="tip-meta-bio">Bio</span></span>
                    <span class="tip-meta-suite"><span class="tip-meta-epices">Épices</span> <span class="tip-meta-tradition">Tradition</span></span>
                </p>
                <div class="tip-card-excerpt">
                    <p class="tip-lede">Parasel-Bio s'utilise comme un sel classique, mais avec des bienfaits supplémentaires.</p>
                    <div class="collapsible-content">
                        <p>Il s'adapte à toutes vos préparations, que ce soit à chaud ou à froid.<br><br>
                        <strong>À chaud</strong><br>
                        • Ajoutez une pincée en début de cuisson pour imprégner vos sauces, ragoûts, soupes et plats mijotés.<br>
                        • Saupoudrez en fin de cuisson pour rehausser le goût des viandes grillées, poissons, légumes sautés ou riz.<br>
                        • Incorporez dans l'eau de cuisson du riz, du couscous ou des pâtes pour un parfum subtil.<br><br>
                        <strong>À froid</strong><br>
                        • Mélangez dans vos vinaigrettes, salades, sauces légères ou marinades.<br>
                        • Rehaussez le goût de vos boissons comme le thé, les infusions ou même certains jus naturels avec une pointe subtile de Parasel-Bio.<br>
                        • Saupoudrez en finition sur vos plats froids (avocats, œufs, sandwichs, légumes crus).<br><br>
                        Une simple pincée de Parasel-Bio suffit pour rehausser les goûts de vos plats et boissons, tout en profitant pleinement des vertus naturelles des 17 épices et du sel iodé.</p>
                    </div>
                </div>
                <button type="button" class="collapsible-toggle tip-readmore-toggle" data-collapsible-toggle aria-expanded="false">Lire la suite</button>
            </div>
        </article>
        </div>
        
    <div class="tips-summary">
        <p>Choisir Parasel-Bio, c'est faire le choix de :</p>
        <ul>
            <li>Prévenir plutôt que guérir : limiter les maladies liées à l'alimentation.</li>
            <li>Consommer local et naturel : valoriser les richesses agricoles du Bénin.</li>
            <li>Éduquer sa famille au goût authentique : transmettre de bonnes habitudes alimentaires dès le plus jeune âge.</li>
        </ul>
    </div>
    <div class="tips-summary-cta">
        <a href="{{ route('products.index') }}" class="home-products-cta-btn">
            <i class="fa-solid fa-basket-shopping home-products-cta-btn__ico" aria-hidden="true"></i>
            Commandez maintenant
        </a>
    </div>
</div>

<div class="testimonials-section">
    <div class="section-header">
        <h2><span class="section-title-text">Témoignages Clients</span></h2>
        <p>Ce que disent nos clients</p>
    </div>

    <div class="testimonials-grid">
@foreach($experiences as $e)
            <div class="testimonial-card collapsible">
                <div class="testimonial-content">
                    <div class="collapsible-content">
                        <p>"{{ $e->content }}"</p>
                    </div>
                    <button type="button" class="collapsible-toggle" data-collapsible-toggle aria-expanded="false">Voir plus</button>
                </div>
                <div class="testimonial-author">
                    <strong>{{ $e->author_name }}</strong>
                </div>
            </div>
@endforeach
        @if($experiences->isEmpty())
            <div class="testimonial-card collapsible">
                <div class="testimonial-content">
                    <div class="collapsible-content">
                        <p>"Les épices Parasel-Bio ont transformé ma cuisine. Un goût authentique et une qualité exceptionnelle !"</p>
                    </div>
                    <button type="button" class="collapsible-toggle" data-collapsible-toggle aria-expanded="false">Voir plus</button>
                </div>
                <div class="testimonial-author">
                    <strong>Marie Dubois</strong>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="cta-section">
    <div class="cta-content">
        <h2>Prêt à découvrir nos épices ?</h2>
        <p>Explorez notre gamme complète d'assaisonnements bio et naturels</p>
        <a href="/produits" class="btn btn-primary btn-large">Voir tous nos produits</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var hero = document.querySelector('.home-page .hero-section');
  if (!hero) return;

  function restartHero() {
    hero.classList.remove('is-animating');
    // force reflow so CSS animations restart
    void hero.offsetWidth;
    hero.classList.add('is-animating');
  }

  // play once on load
  restartHero();

  if (!('IntersectionObserver' in window)) return;
  var lastRunAt = 0;

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      // debounce to avoid rapid retriggers while scrolling
      var now = Date.now();
      if (now - lastRunAt < 1200) return;
      lastRunAt = now;
      restartHero();
    });
  }, { threshold: 0.6 });

  io.observe(hero);
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (!('IntersectionObserver' in window)) return;

  var btns = document.querySelectorAll('.home-page .offline-order-btn');
  if (!btns.length) return;

  function restartTyping(btn) {
    btn.classList.remove('is-typing');
    void btn.offsetWidth;
    btn.classList.add('is-typing');
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      restartTyping(entry.target);
    });
  }, { threshold: 0.7 });

  btns.forEach(function (btn) { io.observe(btn); });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Déplier/replier le texte dans les cartes (mobile uniquement via CSS)
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-collapsible-toggle]');
    if (!btn) return;
    var card = btn.closest('.collapsible');
    if (!card) return;
    var expanded = card.classList.toggle('is-expanded');
    btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    var isTipReadmore = btn.classList.contains('tip-readmore-toggle');
    btn.textContent = expanded ? 'Voir moins' : (isTipReadmore ? 'Lire la suite' : 'Voir plus');
  });

  // Pas de "Voir plus" pour les témoignages très courts (<= 3 lignes)
  try {
    if (window.matchMedia && window.matchMedia('(max-width: 768px)').matches) {
      var testimonials = document.querySelectorAll('.home-page .testimonials-grid .testimonial-card.collapsible');
      testimonials.forEach(function (card) {
        var content = card.querySelector('.collapsible-content');
        var toggle = card.querySelector('[data-collapsible-toggle]');
        var p = content ? content.querySelector('p') : null;
        if (!content || !toggle || !p) return;

        // Mesure en "lignes" via line-height
        var cs = window.getComputedStyle(p);
        var lh = parseFloat(cs.lineHeight);
        if (!lh || isNaN(lh)) {
          var fs = parseFloat(cs.fontSize) || 14;
          lh = fs * 1.4;
        }

        // Forcer la mesure sur l'état tronqué
        var prevMax = content.style.maxHeight;
        content.style.maxHeight = 'none';
        var fullHeight = content.scrollHeight;
        content.style.maxHeight = prevMax;

        var threeLines = (lh * 3) + 4; // marge
        if (fullHeight <= threeLines) {
          card.classList.add('no-toggle');
          toggle.style.display = 'none';
        }
      });
    }
  } catch (e) {}
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (!('IntersectionObserver' in window)) return;

  if (document.body && document.body.classList.contains('home-page')) {
    document.body.classList.add('home-reveal-ready');
  }

  var headers = document.querySelectorAll('.home-page .section-header');
  if (!headers.length) return;

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-revealed');
      } else {
        // Permet de rejouer l'animation quand on revient
        entry.target.classList.remove('is-revealed');
      }
    });
  }, { threshold: 0.35, rootMargin: '0px 0px -10% 0px' });

  headers.forEach(function (h) { io.observe(h); });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var showcase = document.getElementById('company-showcase');
  if (!showcase || !document.body.classList.contains('home-page')) return;

  function setShowcaseVisible(isVisible) {
    if (isVisible) {
      showcase.classList.add('company-showcase--visible');
    } else {
      // Permet de rejouer l'entrée quand on revient après un scroll
      showcase.classList.remove('company-showcase--visible');
      showcase.classList.remove('company-showcase--scroll-idle');
    }
  }

  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    setShowcaseVisible(true);
    return;
  }

  if (!('IntersectionObserver' in window)) {
    setShowcaseVisible(true);
    return;
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      setShowcaseVisible(!!entry.isIntersecting);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

  io.observe(showcase);

  /* Micro-animation quand le défilement s’arrête (section visible), avec cooldown */
  var scrollIdleTimer = null;
  var lastIdlePulseAt = 0;
  var userHasScrolled = false;
  var IDLE_WAIT_MS = 200;
  var IDLE_COOLDOWN_MS = 4200;
  var IDLE_CLASS_MS = 1150;

  function isShowcaseInViewport() {
    var rect = showcase.getBoundingClientRect();
    var vh = window.innerHeight || document.documentElement.clientHeight;
    return rect.top < vh * 0.78 && rect.bottom > vh * 0.18;
  }

  window.addEventListener('scroll', function () {
    userHasScrolled = true;
    showcase.classList.remove('company-showcase--scroll-idle');
    clearTimeout(scrollIdleTimer);
    scrollIdleTimer = setTimeout(function () {
      if (!showcase.classList.contains('company-showcase--visible')) return;
      if (!userHasScrolled) return;
      if (!isShowcaseInViewport()) return;
      var now = Date.now();
      if (now - lastIdlePulseAt < IDLE_COOLDOWN_MS) return;
      lastIdlePulseAt = now;
      showcase.classList.add('company-showcase--scroll-idle');
      setTimeout(function () {
        showcase.classList.remove('company-showcase--scroll-idle');
      }, IDLE_CLASS_MS);
    }, IDLE_WAIT_MS);
  }, { passive: true });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var shop = document.getElementById('home-products-showcase');
  if (!shop || !document.body.classList.contains('home-page')) return;

  function setShopVisible(isVisible) {
    if (isVisible) {
      shop.classList.add('home-products-showcase--revealed');
    } else {
      shop.classList.remove('home-products-showcase--revealed');
      shop.classList.remove('home-products-showcase--scroll-idle');
    }
  }

  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    setShopVisible(true);
  } else if (!('IntersectionObserver' in window)) {
    setShopVisible(true);
  } else {
    var ioReveal = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        setShopVisible(!!entry.isIntersecting);
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -6% 0px' });
    ioReveal.observe(shop);
  }

  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var scrollIdleTimer = null;
  var lastIdlePulseAt = 0;
  var userHasScrolled = false;
  var IDLE_WAIT_MS = 200;
  var IDLE_COOLDOWN_MS = 4500;
  var IDLE_CLASS_MS = 1000;

  function isShopInViewport() {
    var rect = shop.getBoundingClientRect();
    var vh = window.innerHeight || document.documentElement.clientHeight;
    return rect.top < vh * 0.82 && rect.bottom > vh * 0.12;
  }

  window.addEventListener('scroll', function () {
    userHasScrolled = true;
    shop.classList.remove('home-products-showcase--scroll-idle');
    clearTimeout(scrollIdleTimer);
    scrollIdleTimer = setTimeout(function () {
      if (!userHasScrolled) return;
      if (!isShopInViewport()) return;
      var now = Date.now();
      if (now - lastIdlePulseAt < IDLE_COOLDOWN_MS) return;
      lastIdlePulseAt = now;
      shop.classList.add('home-products-showcase--scroll-idle');
      setTimeout(function () {
        shop.classList.remove('home-products-showcase--scroll-idle');
      }, IDLE_CLASS_MS);
    }, IDLE_WAIT_MS);
  }, { passive: true });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (!document.body.classList.contains('home-page')) return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (!('IntersectionObserver' in window)) return;

  var hero = document.querySelector('.home-page .hero-section');
  var distribution = document.querySelector('.home-page .distribution-hero');
  var tips = document.querySelector('.home-page .tips-section');

  function bindInView(el, className, threshold, rootMargin, restartOnEnter) {
    if (!el) return;
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          if (restartOnEnter && el.classList.contains(className)) {
            // Force un redémarrage net de l'animation (utile quand on remonte/descend souvent)
            el.classList.remove(className);
            void el.offsetWidth;
          }
          el.classList.add(className);
        } else {
          // Permet de rejouer l'animation sans recharger
          el.classList.remove(className);
        }
      });
    }, { threshold: threshold, rootMargin: rootMargin });
    io.observe(el);
  }

  bindInView(hero, 'hero--inview', 0.4, '0px 0px -10% 0px', false);
  bindInView(distribution, 'distribution-hero--inview', 0.2, '0px 0px -10% 0px', false);
  // Conseils : déclenche plus tôt pour qu'on l'entrevoie avant (évite "grand blanc")
  bindInView(tips, 'tips--inview', 0.08, '0px 0px -8% 0px', true);
});
</script>
@endpush

