<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;

class ClientsController extends Controller
{
    /**
     * Page clients : vue PHP pure (pas de Blade) pour affichage correct.
     * Liste des comptes clients réels (rôle = client).
     */
    public function index(): Response
    {
        $users = User::where('role', 'client')
            ->where('email', 'not like', '%@example.%')
            ->withCount('orders')
            ->latest()
            ->paginate(20);

        return response()->view('admin.users.clients_plain', compact('users'), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
