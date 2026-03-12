<?php

namespace App\Enums;

enum TravelOrderStatusEnum: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Cancelled = 'cancelled';
}
