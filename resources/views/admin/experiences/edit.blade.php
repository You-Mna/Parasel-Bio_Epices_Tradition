@extends('layouts.admin')

@section('content')
<h1>Modifier témoignage</h1>
<form method="POST" action="{{ route('admin.experiences.update', $experience) }}" class="card">
    @csrf @method('PUT')
    <label>Nom de l'auteur</label>
    <input name="author_name" value="{{ $experience->author_name }}" required>
    
    <label>Contenu du témoignage</label>
    <textarea name="content" required>{{ $experience->content }}</textarea>
    
    <label>
        <input type="checkbox" name="is_published" value="1" {{ $experience->is_published ? 'checked' : '' }}>
        Publier ce témoignage
    </label>
    
    <div style="display: flex; gap: 12px; margin-top: 16px;">
        <button class="btn" type="submit">Mettre à jour</button>
        <a class="btn secondary" href="{{ route('admin.experiences.index') }}">Annuler</a>
    </div>
</form>
@endsection
