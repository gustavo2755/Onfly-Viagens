<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource de serializacao de log de status de pedido.
 */
class TravelOrderStatusLogResource extends JsonResource
{
    /**
     * Converte o log para array de resposta.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'travel_order_id' => $this->travel_order_id,
            'admin_user_id' => $this->admin_user_id,
            'from_status' => $this->from_status,
            'to_status' => $this->to_status,
            'created_at' => optional($this->created_at)->toISOString(),
            'admin_user' => $this->whenLoaded('adminUser', fn () => new AuthenticatedUserResource($this->adminUser)),
        ];
    }
}
