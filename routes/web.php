<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ExperienceController as AdminExperienceController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\ShipmentController as AdminShipmentController;
use App\Http\Controllers\Admin\PointDeVenteController as AdminPointDeVenteController;
use App\Http\Controllers\Admin\SalesReportController as AdminSalesReportController;
use App\Http\Controllers\Admin\ClientsController as AdminClientsController;

Route::get('/', function () {
    return app(PublicController::class)->home();
})->name('home');

// Public pages
Route::get('/produits', [PublicController::class, 'products'])->name('products.index');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/points-de-vente', [PublicController::class, 'pointsDistribution'])->name('points.distribution');
Route::post('/contact', [PublicController::class, 'submitContact'])->name('contact.submit');

// Webhook routes (no authentication required, pas de CSRF, mais rate limité)
Route::post('/webhook/payment', [PaymentController::class, 'handleWebhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->middleware('throttle:60,1')
    ->name('payment.webhook');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Panier accessible sans authentification (session pour invités)
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/panier/modifier-quantite/{cartKey}', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::post('/panier/supprimer/{cartKey}', [CartController::class, 'remove'])->name('cart.remove');

// Client area
Route::middleware('auth')->group(function () {
    Route::get('/mon-compte', [ClientController::class, 'account'])->name('client.account');
    Route::get('/mes-commandes', [ClientController::class, 'orders'])->name('client.orders');

    // Checkout (auth requis)
    Route::get('/commande', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/commande', [CartController::class, 'processOrder'])->name('order.process');
    Route::get('/commande/payer/{order}', [CartController::class, 'paymentPage'])->name('checkout.pay');
    
    // Payment routes
    // Paiement en ligne (FedaPay) + callbacks
    Route::post('/payment/initialize', [PaymentController::class, 'initializePayment'])
        ->middleware('throttle:20,1')
        ->name('payment.initialize');
    Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
    Route::post('/payment/callback', [PaymentController::class, 'handleCallback']);
    Route::get('/payment/status/{transaction_id}', [PaymentController::class, 'checkPaymentStatus'])
        ->middleware('throttle:60,1')
        ->name('payment.status');
    Route::get('/payment/methods', [PaymentController::class, 'getPaymentMethods'])
        ->middleware('throttle:60,1')
        ->name('payment.methods');

    // Page de paiement annulé (redirige vers le panier avec un message)
    Route::get('/payment/cancelled', function () {
        return redirect()->route('cart.index')->with('error', 'Le paiement a été annulé.');
    })->name('payment.cancelled');
});

// Admin
Route::prefix('admin')->as('admin.')->middleware(['auth','admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class)->except(['destroy']);
    Route::post('products/{product}/toggle-stock', [AdminProductController::class, 'toggleStock'])->name('products.toggle-stock');
    Route::post('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [AdminOrderController::class, 'store'])->name('orders.store');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::resource('experiences', AdminExperienceController::class)->only(['index','store','edit','update','destroy']);
    Route::resource('points-de-vente', AdminPointDeVenteController::class);
    Route::resource('shipments', AdminShipmentController::class);
    Route::get('reports', [AdminSalesReportController::class, 'index'])->name('reports.index');
    Route::get('reports/compare', [AdminSalesReportController::class, 'compare'])->name('reports.compare');
    Route::get('reports/export-pdf', [AdminSalesReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('users', [AdminClientsController::class, 'index'])->name('users.index');
});
