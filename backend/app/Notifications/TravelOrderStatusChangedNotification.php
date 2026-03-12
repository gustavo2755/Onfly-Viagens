<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TravelOrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly TravelOrder $travelOrder)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'travel_order_id' => $this->travelOrder->id,
            'status' => $this->travelOrder->status->value,
            'message' => 'Your travel order #'.$this->travelOrder->id.' is now '.$this->travelOrder->status->value.'.',
        ];
    }
}
