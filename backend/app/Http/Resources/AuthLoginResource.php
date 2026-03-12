<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource de resposta de login com token e usuario.
 */
class AuthLoginResource extends JsonResource
{
    /**
     * Converte a resposta de login para array.
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this['token'],
            'user' => new AuthenticatedUserResource($this['user']),
        ];
    }
}
