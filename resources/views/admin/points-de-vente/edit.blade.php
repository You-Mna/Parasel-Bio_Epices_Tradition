@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Modifier le point de vente</h1>
        <p class="admin-page-subtitle">{{ $point->name }}</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.points-de-vente.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<form method="POST" action="/admin/points-de-vente/{{ $point->id }}" class="card admin-form admin-form--wide">
    @csrf
    @method('PUT')
    @php($useOld = $errors->any())
    <div class="form-row">
        <div class="form-group" style="flex:0 0 auto;">
            <label for="name">Nom *</label>
            <input type="text" name="name" id="name" value="{{ $useOld ? old('name') : $point->name }}" required>
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label for="code">Code (référence)</label>
            <input type="text" name="code" id="code" value="{{ $useOld ? old('code') : ($point->code ?? '') }}">
            @error('code')<span class="form-error">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group" style="flex:0 0 auto;">
            <label for="phone">Téléphone *</label>
            <input type="tel" name="phone" id="phone" value="{{ $useOld ? old('phone') : ($point->phone ?? '') }}" pattern="[0-9]+" inputmode="numeric" required>
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label for="city">Ville</label>
            <input type="text" name="city" id="city" value="{{ $useOld ? old('city') : ($point->city ?? '') }}">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group" style="flex:0 0 auto;">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ $useOld ? old('email') : ($point->email ?? '') }}">
            @error('email')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label for="address">Adresse</label>
            <input type="text" name="address" id="address" value="{{ $useOld ? old('address') : ($point->address ?? '') }}">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-admin success"><i class="fa-solid fa-check"></i> Enregistrer</button>
        <a href="{{ route('admin.points-de-vente.index') }}" class="btn-admin secondary">Annuler</a>
    </div>
</form>
@endsection
