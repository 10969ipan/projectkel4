<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    private $order;
    private $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = '';
        $icon = '';
        
        if ($this->status === 'processing') {
            $message = "Pesanan #{$this->order->order_number} sedang dikirim!";
            $icon = 'fa-truck';
        } elseif ($this->status === 'completed') {
            $message = "Pesanan #{$this->order->order_number} telah sampai. Terima kasih!";
            $icon = 'fa-check-circle';
        }

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->status,
            'message' => $message,
            'icon' => $icon
        ];
    }
}
