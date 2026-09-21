@extends('layouts.app')

@section('content')
<div class="distribution-page">
    <div class="distribution-page__intro">
        <nav class="distribution-page__back" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}" class="distribution-page__back-link">← Accueil</a>
        </nav>
        <h1 class="distribution-page__title">Points de Distribution</h1>
        <p class="distribution-page__lead">Nos produits sont disponibles dans plusieurs pays et régions. Retrouvez ci-dessous les adresses et relais au Bénin et dans la diaspora.</p>
    </div>

    <div class="distribution-section distribution-section--page">
        @include('public.partials.distribution-lists')
    </div>

    <div class="distribution-page__cta" aria-label="En savoir plus">
        <div class="distribution-page__cta-inner">
            <div class="distribution-page__cta-text">
                <h2 class="distribution-page__cta-title">Une question sur nos points de vente&nbsp;?</h2>
                <p class="distribution-page__cta-subtitle">Contactez-nous, on vous répond rapidement.</p>
            </div>
            <a class="btn btn-primary distribution-page__cta-btn" href="{{ url('/contact') }}">
                <i class="fa-solid fa-envelope-open-text" aria-hidden="true"></i>
                <span>En savoir plus</span>
            </a>
        </div>
    </div>
</div>
@endsection
