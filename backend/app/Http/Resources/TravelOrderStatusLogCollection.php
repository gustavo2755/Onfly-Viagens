<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TravelOrderStatusLogCollection extends ResourceCollection
{
    public $collects = TravelOrderStatusLogResource::class;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
