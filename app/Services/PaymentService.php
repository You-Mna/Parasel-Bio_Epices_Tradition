<?php

namespace App\Services;

class PaymentService
{
    protected string $provider;

    public function __construct(string $provider = 'feda_pay')
    {
        $this->provider = $provider;
    }

    /**
     * Implémentation minimale pour compatibilité, non utilisée dans le flux FedaPay actuel.
     */
    public function initializePayment(array $data): array
    {
        return [
            'success' => false,
            'payment_url' => null,
            'transaction_id' => null,
            'reference' => $data['reference'] ?? null,
        ];
    }

    /**
     * Implémentation minimale pour compatibilité, non utilisée dans le flux FedaPay actuel.
     */
    public function verifyPayment(string $transactionId): array
    {
        return [
            'success' => false,
            'status' => 'pending',
        ];
    }
}

