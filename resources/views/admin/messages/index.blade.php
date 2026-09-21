@extends('layouts.admin')

@section('content')
<!-- Header de la page -->
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Formulaire de Contact</h1>
        <p class="admin-page-subtitle">Consultez les messages de vos clients</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Tableau de bord
        </a>
    </div>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table admin-table--messages">
        <thead>
            <tr>
                <th>Client</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        @forelse($messages as $m)
        @php
            $raw = $m->content ?? '';
            // On gère deux formats possibles :
            // 1) "Sujet: xxx\nMessage: yyy"
            // 2) "Sujet: xxx\nyyy..." (sans "Message:")
            $subject = '';
            $body = '';

            if (\Illuminate\Support\Str::contains($raw, 'Message:')) {
                $subject = \Illuminate\Support\Str::of($raw)->after('Sujet:')->before('Message:')->trim();
                $body = \Illuminate\Support\Str::of($raw)->after('Message:')->trim();
            } else {
                $lines = preg_split("/\r\n|\n|\r/", trim($raw));
                if (!empty($lines)) {
                    $subject = \Illuminate\Support\Str::of($lines[0])->after('Sujet:')->trim();
                    if (count($lines) > 1) {
                        $body = trim(implode("\n", array_slice($lines, 1)));
                    }
                }
            }
        @endphp
            <tr>
                <td class="col-client" data-label="Client">
                    <div class="cell-main">{{ $m->name }}</div>
                </td>
                <td class="col-email" data-label="Email">
                    <div class="cell-main">{{ $m->email }}</div>
                </td>
                <td class="col-phone" data-label="Téléphone">
                    <div class="cell-main">{{ $m->phone }}</div>
                </td>
                <td class="col-message" data-label="Message">
                    <div class="cell-main">
                        @if($subject !== '')
                            <div class="msg-subject"><strong>Sujet :</strong> {{ $subject }}</div>
                        @endif
                        @if($body !== '')
                            <div class="msg-body">{{ $body }}</div>
                        @else
                            <div class="msg-body">{{ $raw }}</div>
                        @endif
                    </div>
                </td>
                <td class="col-date" data-label="Date">
                    <div class="cell-main">{{ $m->created_at->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="admin-table-empty">
                    Aucun message pour le moment.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="admin-table-pagination">
    {{ $messages->links('vendor.pagination.admin') }}
</div>
@endsection

