<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'payee_en_ligne' => 'Payée en ligne',
            'payee' => 'Payée en ligne',
            'a_la_livraison' => 'À la livraison',
            'en_cours' => 'En attente paiement',
            'livree_payee' => 'Livrée payée',
            'livree' => 'Livrée payée',
            'annulee' => 'Annulée',
        ];

        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return (new MailMessage)
            ->subject('Mise à jour de votre commande #' . $this->order->id)
            ->view('emails.order-status-updated', [
                'user' => $notifiable,
                'order' => $this->order,
                'oldStatusLabel' => $oldStatusLabel,
                'newStatusLabel' => $newStatusLabel
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
