<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource de serializacao de notificacao.
 */
class NotificationResource extends JsonResource
{
    /**
     * Converte a notificacao para array de resposta.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
            'read_at' => optional($this->read_at)?->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
