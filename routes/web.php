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

Route::get('/', function () {
    return app(PublicController::class)->home();
})->name('home');

// Public pages
Route::get('/produits', [PublicController::class, 'products'])->name('products.index');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'submitContact'])->name('contact.submit');

// Webhook routes (no authentication required)
Route::post('/webhook/payment', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
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
    
    // Payment routes
    Route::post('/payment/initialize', [PaymentController::class, 'initializePayment'])->name('payment.initialize');
    Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
    Route::post('/payment/callback', [PaymentController::class, 'handleCallback']);
    Route::get('/payment/status/{transaction_id}', [PaymentController::class, 'checkPaymentStatus'])->name('payment.status');
    Route::get('/payment/methods', [PaymentController::class, 'getPaymentMethods'])->name('payment.methods');
});

// Admin
Route::prefix('admin')->as('admin.')->middleware(['auth','admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class)->except(['destroy']);
    Route::post('products/{product}/toggle-stock', [AdminProductController::class, 'toggleStock'])->name('products.toggle-stock');
    Route::post('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::resource('experiences', AdminExperienceController::class)->only(['index','store','edit','update','destroy']);
    
});
