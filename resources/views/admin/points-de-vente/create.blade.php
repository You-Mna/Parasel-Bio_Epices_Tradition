@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Nouveau point de vente</h1>
        <p class="admin-page-subtitle">Ajouter un point de vente pour les expéditions</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.points-de-vente.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.points-de-vente.store') }}" class="card admin-form admin-form--wide">
    @csrf
    <div class="form-row">
        <div class="form-group" style="flex:0 0 auto;">
            <label for="name">Nom *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label for="code">Code (référence)</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="ex. PDV-DAKAR-01">
            @error('code')<span class="form-error">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group" style="flex:0 0 auto;">
            <label for="phone">Téléphone *</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" pattern="[0-9]+" inputmode="numeric" required>
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label for="city">Ville</label>
            <input type="text" name="city" id="city" value="{{ old('city') }}">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group" style="flex:0 0 auto;">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}">
            @error('email')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label for="address">Adresse *</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" required>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-admin success"><i class="fa-solid fa-check"></i> Créer</button>
        <a href="{{ route('admin.points-de-vente.index') }}" class="btn-admin secondary">Annuler</a>
    </div>
</form>
@endsection
