@extends('layouts.app')

@section('content')
<div class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">
            <span class="logo-parasel">
                <span class="logo-para">Para</span>
                <span class="logo-s">s</span>
                <span class="logo-el">el</span>
            </span>-<span class="bio-text">BIO</span> <span class="logo-epices">Épices et Tradition</span>
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
        <h2>Notre entreprise</h2>
        <p>Une référence dans les assaisonnements naturels et bio</p>
    </div>
    
    <div class="company-grid">
        <div class="company-card">
            <h3>Notre identité</h3>
            <h4>Un assaisonnement authentique et bio</h4>
            <p>Parasel-Bio est une entreprise béninoise spécialisée dans la production d'assaisonnements 100 % naturels. Son produit phare, le sel marin assaisonné, associe 17 épices et légumes cultivés localement à un sel iodé récolté de manière traditionnelle. Cette combinaison unique conjugue héritage culinaire africain et procédés modernes certifiés bio, offrant ainsi des produits sains, savoureux et bénéfiques pour la santé.</p>
        </div>
        
        <div class="company-card">
            <h3>Notre engagement</h3>
            <h4>Des assaisonnements sains, sans compromis</h4>
            <p>Les produits Parasel-Bio sont rigoureusement analysés en laboratoire et conformes aux normes d'hygiène et de sécurité. En 2017, l'entreprise a reçu le Prix TIB/MESRS en Innovation Agroalimentaire et Santé, reconnaissance de son sérieux et de son expertise. Chaque formulation est conçue pour répondre aux attentes d'une clientèle exigeante : des assaisonnements sans additifs chimiques, alliant goût, équilibre nutritionnel et sécurité alimentaire.</p>
        </div>
        
        <div class="company-card">
            <h3>Notre vision</h3>
            <h4>Une marque locale tournée vers l'international</h4>
            <p>En valorisant les ressources agricoles du Bénin, Parasel-Bio soutient les producteurs locaux et dynamise les circuits courts. Mais sa mission dépasse les frontières : nos assaisonnements sont déjà disponibles sur plusieurs marchés internationaux et visent à répondre à une demande croissante pour des produits bio authentiques. L'ambition est claire : faire de Parasel-Bio une référence mondiale des assaisonnements naturels, capable d'influencer positivement les habitudes alimentaires.</p>
        </div>
    </div>
</div>

<div class="distribution-section">
    <div class="section-header">
        <h2>Points de Distribution</h2>
        <p>Nos produits sont disponibles dans plusieurs pays et régions</p>
    </div>
    
    <div class="distribution-grid">
        <div class="region-card">
            <h3>Bénin</h3>
            <div class="benin-columns">
                <div class="benin-column">
                    <div class="city-section">
                        <h4>Cotonou</h4>
                        <ul class="location-list">
                            <li>Aliment Sain – Ganhi Galerie</li>
                            <li>Label Bénin</li>
                            <li>Franc Prix</li>
                            <li>Le Local</li>
                            <li>Grand Marché</li>
                            <li>O Bénin Market</li>
                        </ul>
                    </div>
                </div>
                
                <div class="benin-column">
                    <div class="city-section">
                        <h4>Agences & réseaux</h4>
                        <ul class="location-list">
                            <li>Moov Bénin</li>
                            <li>Allada – Cantine des Hôpitaux Nazareth</li>
                            <li>Tchaourou – Chez Ibo</li>
            </ul>
        </div>
        
                    <div class="city-section">
                        <h4>Pharmacies</h4>
                        <ul class="location-list">
                            <li>Midombo</li>
                            <li>Kandi – AlHeri</li>
                            <li>Malanville</li>
                            <li>Parakou – Pharmacie de la Gare</li>
            </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="region-card">
            <h3>Diaspora</h3>
            <div class="diaspora-columns">
                <div class="diaspora-column">
                    <div class="city-section">
                        <h4>Afrique</h4>
                        <ul class="location-list">
                            <li>Sénégal – Dakar</li>
                            <li>Gabon – Dr. Biokou Adiho</li>
                            <li>Gabon – M. Khalifa Djamal Agnidé</li>
                            <li>Burkina Faso – M. Mandy</li>
                            <li>Côte d'Ivoire – Abidjan</li>
                        </ul>
                    </div>
                </div>
                
                <div class="diaspora-column">
                    <div class="city-section">
                        <h4>Europe</h4>
                        <ul class="location-list">
                            <li>France – Paris</li>
            </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="command-direct-section">
        <div class="command-card">
            <h3>Commande directe</h3>
            <p>Nos produits sont également disponibles en commande directe pour les ménages, avec livraison à domicile, quel que soit le pays de destination.</p>
        </div>
    </div>
</div>

<div class="tips-section">
    <div class="section-header">
        <h2>Conseils Nutritionnels</h2>
        <p>Adoptez une alimentation saine et équilibrée</p>
    </div>
    
    <div class="tips-grid">
        <div class="tip-card">
            <h3>Parasel-Bio, bon pour la santé</h3>
            <p>Contrairement aux cubes chimiques riches en glutamate, exhausteurs de goût, sels raffinés et additifs artificiels, Parasel-Bio est élaboré à partir de 17 épices et légumes locaux associés à un sel marin iodé.<br><br>
            Ce mélange apporte non seulement une saveur authentique aux plats, mais aussi une véritable protection pour l'organisme :<br>
            • Préserve le foie et les reins, souvent fragilisés par les excès d'additifs chimiques.<br>
            • Contribue à réguler la tension artérielle grâce à son sel naturel iodé.<br>
            • Renforce l'immunité grâce aux antioxydants présents dans les épices.</p>
        </div>
        
        <div class="tip-card">
            <h3>Les bienfaits des 17 épices</h3>
            <p>Chaque ingrédient de Parasel-Bio a été sélectionné pour soutenir un organe ou une fonction de l'organisme :<br><br>
            • Certains favorisent la digestion et apaisent les ballonnements.<br>
            • D'autres stimulent la circulation sanguine et renforcent le cœur.<br>
            • Plusieurs épices sont reconnues pour leurs propriétés anti-inflammatoires et antioxydantes, retardant le vieillissement cellulaire.<br><br>
            Résultat : un assaisonnement qui ne se contente pas de relever les plats, mais qui agit comme un allié quotidien de votre bien-être.</p>
        </div>
        
        <div class="tip-card">
            <h3>Les dangers des additifs chimiques</h3>
            <p>Les bouillons industriels et assaisonnements chimiques sont souvent riches en glutamate monosodique (MSG), exhausteurs de goût, colorants artificiels et conservateurs.<br><br>
            Leur consommation excessive peut entraîner :<br>
            • Hypertension et maladies cardiovasculaires dues à la forte teneur en sel raffiné.<br>
            • Risques de cirrhose et d'atteintes hépatiques (cas réel à l'origine de la création de Parasel-Bio).<br>
            • Puberté précoce chez les jeunes filles, avec l'apparition des menstrues dès l'âge de 8 ans.<br>
            • Dépendance gustative : le palais s'habitue au goût artificiel et les plats naturels paraissent fades.<br><br>
            Parasel-Bio est né pour offrir une alternative saine, sans danger pour la santé, respectueuse de l'organisme et de l'environnement.</p>
        </div>
        
        <div class="tip-card">
            <h3>Comment utiliser Parasel-Bio au quotidien ?</h3>
            <p>Parasel-Bio s'utilise comme un sel classique, mais avec des bienfaits supplémentaires. Il s'adapte à toutes vos préparations, que ce soit à chaud ou à froid.<br><br>
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
        
    <div class="tips-summary">
        <p>Choisir Parasel-Bio, c'est faire le choix de :</p>
        <ul>
            <li>Prévenir plutôt que guérir : limiter les maladies liées à l'alimentation.</li>
            <li>Consommer local et naturel : valoriser les richesses agricoles du Bénin.</li>
            <li>Éduquer sa famille au goût authentique : transmettre de bonnes habitudes alimentaires dès le plus jeune âge.</li>
        </ul>
    </div>
</div>

<div class="testimonials-section">
    <div class="section-header">
        <h2>Témoignages Clients</h2>
        <p>Ce que disent nos clients satisfaits</p>
    </div>

    <div class="testimonials-grid">
@foreach($experiences as $e)
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <p>"{{ $e->content }}"</p>
                </div>
                <div class="testimonial-author">
                    <strong>{{ $e->author_name }}</strong>
                </div>
            </div>
@endforeach
        @if($experiences->isEmpty())
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <p>"Les épices Parasel-Bio ont transformé ma cuisine. Un goût authentique et une qualité exceptionnelle !"</p>
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

