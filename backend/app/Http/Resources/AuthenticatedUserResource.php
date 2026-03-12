<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource de serializacao do usuario autenticado.
 */
class AuthenticatedUserResource extends JsonResource
{
    /**
     * Converte usuario para resposta padronizada.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames()->first(),
        ];
    }
}
