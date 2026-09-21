{{-- Image carrée vitrine (accueil) — logique alignée sur la page Produits --}}
@php
    use Illuminate\Support\Str;
@endphp
@if($product->name === 'Parasel-Bio Marinade')
    <img
        src="{{ asset('images/parasel115g.jpg') }}"
        alt=""
        class="home-product-card__img"
        loading="lazy"
        decoding="async"
    >
@elseif($product->image)
    <img
        src="{{ Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset('images/' . $product->image) }}"
        alt=""
        class="home-product-card__img"
        loading="lazy"
        decoding="async"
    >
@else
    <div class="home-product-card__placeholder" aria-hidden="true"></div>
@endif
