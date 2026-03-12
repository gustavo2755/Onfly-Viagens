<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Collection de logs de status para resposta paginada.
 */
class TravelOrderStatusLogCollection extends ResourceCollection
{
    public $collects = TravelOrderStatusLogResource::class;

    /**
     * Converte a collection para array de resposta.
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
