<?php

namespace App\Support\Concerns;

use App\Enums\TravelOrderStatusEnum;

trait NormalizesTravelOrderStatus
{
    protected function normalizeStatus(mixed $status): string
    {
        if ($status instanceof TravelOrderStatusEnum) {
            return $status->value;
        }

        return (string) $status;
    }
}
