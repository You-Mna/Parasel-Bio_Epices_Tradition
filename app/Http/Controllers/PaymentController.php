<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService('feda_pay'); // Default to FedaPay
    }

    /**
     * Initialize payment for an order
     */
    public function initializePayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'payment_method' => 'required|in:mtn_momo,moov_money,orange_money',
                'phone_number' => 'required|string|min:8|max:15',
            ]);

            $order = Order::with('user')->findOrFail($validated['order_id']);

            // Check if order belongs to authenticated user
            if ($order->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Vérifier si la commande est déjà payée / traitée
            if (in_array($order->status, ['payee', 'payee_en_ligne', 'livree', 'livree_payee'], true)) {
                return response()->json(['error' => 'Order already paid or processed'], 400);
            }

            // Map payment method to provider
            $providerMap = [
                'mtn_momo' => 'mtn',
                'moov_money' => 'moov',
                'orange_money' => 'orange',
            ];

            $paymentData = [
                'amount' => $order->total_amount,
                'currency' => 'XOF',
                'description' => "Commande Parasel Bio #{$order->id}",
                'reference' => 'PARASEL_' . $order->id . '_' . Str::random(8),
                'callback_url' => route('payment.callback'),
                'customer' => [
                    'firstname' => $order->user->first_name ?? 'Client',
                    'lastname' => $order->user->last_name ?? 'Parasel',
                    'email' => $order->user->email,
                    'phone_number' => $validated['phone_number'],
                ],
                'provider' => $providerMap[$validated['payment_method']],
            ];

            $paymentResult = $this->paymentService->initializePayment($paymentData);

            if ($paymentResult['success']) {
                // Update order with payment information
                $order->update([
                    'payment_method' => $validated['payment_method'],
                    'payment_reference' => $paymentResult['reference'],
                    'payment_transaction_id' => $paymentResult['transaction_id'],
                    // On garde le statut métier tel quel ici (il restera "en_cours")
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $paymentResult['payment_url'],
                    'transaction_id' => $paymentResult['transaction_id'],
                    'reference' => $paymentResult['reference'],
                ]);
            }

            return response()->json(['error' => 'Payment initialization failed'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment initialization failed'], 500);
        }
    }

    /**
     * Handle payment callback/webhook
     */
    public function handleCallback(Request $request): RedirectResponse
    {
        try {
            $transactionId = $request->input('transaction_id') ?? $request->input('tx_ref');
            
            if (!$transactionId) {
                return redirect()->route('cart.index')->with('error', 'Invalid payment callback');
            }

            // Find order by transaction ID
            $order = Order::where('payment_transaction_id', $transactionId)->first();
            
            if (!$order) {
                return redirect()->route('cart.index')->with('error', 'Order not found');
            }

            // Verify payment with provider
            $verificationResult = $this->paymentService->verifyPayment($transactionId);

            if ($verificationResult['success'] && $verificationResult['status'] === 'approved') {
                // Paiement en ligne réussi : statut automatique "payée en ligne"
                $order->update([
                    'status' => 'payee_en_ligne',
                    'payment_status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Clear cart (session + DB pour cet utilisateur)
                session()->forget('cart');
                if ($order->user_id) {
                    CartItem::where('user_id', $order->user_id)->delete();
                }

                // Send notification to user
                $order->user->notify(new \App\Notifications\OrderStatusUpdated($order, 'en_cours', 'payee_en_ligne'));

                return redirect()->route('client.orders')->with('success', 'Paiement effectué avec succès !');
            } else {
                // Paiement échoué : la commande est considérée comme "annulee"
                $order->update([
                    'status' => 'annulee',
                    'payment_status' => 'failed',
                ]);

                return redirect()->route('cart.index')->with('error', 'Paiement échoué. Veuillez réessayer.');
            }

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Erreur lors du traitement du paiement');
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'transaction_id' => 'required|string',
            ]);

            $order = Order::where('payment_transaction_id', $validated['transaction_id'])->first();
            
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $verificationResult = $this->paymentService->verifyPayment($validated['transaction_id']);

            return response()->json([
                'success' => true,
                'status' => $verificationResult['status'],
                'order_status' => $order->status,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to check payment status'], 500);
        }
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods(): JsonResponse
    {
        $methods = [
            [
                'id' => 'mtn_momo',
                'name' => 'MTN Mobile Money',
                'provider' => 'mtn',
                'icon' => 'fas fa-mobile-alt',
                'available' => true,
            ],
            [
                'id' => 'moov_money',
                'name' => 'Moov Money',
                'provider' => 'moov',
                'icon' => 'fas fa-mobile-alt',
                'available' => true,
            ],
            [
                'id' => 'orange_money',
                'name' => 'Orange Money',
                'provider' => 'orange',
                'icon' => 'fas fa-mobile-alt',
                'available' => false, // Not implemented yet
            ],
        ];

        return response()->json([
            'success' => true,
            'methods' => $methods,
        ]);
    }

    /**
     * Handle webhook from payment provider
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();

            // Nom de l'événement (FedaPay envoie "name" ou "event", ex: "transaction.approved")
            $event = $request->input('event') ?? $request->input('name');

            // Récupérer l'ID de commande depuis les metadata (plusieurs structures possibles selon FedaPay)
            $orderIdFromMetadata = $request->input('entity.custom_metadata.order_id')
                ?? $request->input('data.transaction.custom_metadata.order_id')
                ?? $request->input('entity.metadata.order_id');

            if ($event === 'transaction.approved' || $event === 'charge.completed') {
                $order = null;

                if ($orderIdFromMetadata) {
                    $order = Order::find((int) $orderIdFromMetadata);
                }


                if ($order && !in_array($order->status, ['payee', 'payee_en_ligne', 'livree', 'livree_payee'], true)) {
                    $oldStatus = $order->status;
                    $order->update([
                        'status' => 'payee_en_ligne',
                    ]);

                    // Vider le panier (session + DB) pour l'utilisateur lié à la commande
                    session()->forget('cart');
                    if ($order->user_id) {
                        CartItem::where('user_id', $order->user_id)->delete();
                    }

                    // Envoyer la notification si le user est bien chargé
                    if ($order->user) {
                        $order->user->notify(new \App\Notifications\OrderStatusUpdated($order, $oldStatus, 'payee_en_ligne'));
                    }
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}

















