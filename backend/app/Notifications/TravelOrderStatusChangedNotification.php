<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TravelOrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly TravelOrder $travelOrder,
        private readonly string $fromStatus,
        private readonly string $toStatus
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'travel_order_id' => $this->travelOrder->id,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
            'message' => 'Seu pedido #'.$this->travelOrder->id.' foi alterado de '.$this->fromStatus.' para '.$this->toStatus.'.',
        ];
    }
}
