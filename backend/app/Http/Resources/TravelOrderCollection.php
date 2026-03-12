<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TravelOrderCollection extends ResourceCollection
{
    public $collects = TravelOrderResource::class;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
