<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Collection de notificacoes para resposta paginada.
 */
class NotificationCollection extends ResourceCollection
{
    public $collects = NotificationResource::class;

    /**
     * Converte a collection para array de resposta.
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
