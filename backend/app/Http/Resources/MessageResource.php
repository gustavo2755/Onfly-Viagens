<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para resposta com mensagem simples.
 */
class MessageResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Converte para array com campo message.
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->resource,
        ];
    }
}
