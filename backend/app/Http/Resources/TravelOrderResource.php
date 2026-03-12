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
    /**
     * Converte entidade de pedido para payload padrao da API.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'requester_name' => $this->requester_name,
            'destination' => $this->destination,
            'departure_date' => optional($this->departure_date)->toDateString(),
            'return_date' => optional($this->return_date)->toDateString(),
            'status' => $this->status instanceof TravelOrderStatusEnum ? $this->status->value : $this->status,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'user' => $this->whenLoaded('user', fn () => new AuthenticatedUserResource($this->user)),
        ];
    }
}
