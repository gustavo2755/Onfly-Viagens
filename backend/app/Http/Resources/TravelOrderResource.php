<?php

namespace App\Http\Resources;

use App\Enums\TravelOrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource de serializacao de pedido de viagem.
 */
class TravelOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'requester_name' => $this->requester_name,
            'destination' => $this->destination,
            'departure_date' => optional($this->departure_date)->toDateString(),
            'return_date' => optional($this->return_date)->toDateString(),
            'departure_date_br' => $this->departure_date_br,
            'return_date_br' => $this->return_date_br,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'status' => $this->status instanceof TravelOrderStatusEnum ? $this->status->value : $this->status,
            'user' => $this->whenLoaded('user', fn () => new AuthenticatedUserResource($this->user)),
        ];
    }
}
