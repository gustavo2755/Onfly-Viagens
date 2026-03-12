<?php

namespace App\Services;

use App\Models\TravelOrderStatusLog;
use App\Services\Contracts\TravelOrderStatusLogQueryServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class TravelOrderStatusLogQueryService implements TravelOrderStatusLogQueryServiceInterface
{
    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return TravelOrderStatusLog::query()
            ->with('adminUser')
            ->when(!empty($filters['travel_order_id']), fn (Builder $query) => $query->where('travel_order_id', $filters['travel_order_id']))
            ->when(!empty($filters['admin_user_id']), fn (Builder $query) => $query->where('admin_user_id', $filters['admin_user_id']))
            ->when(!empty($filters['to_status']), fn (Builder $query) => $query->where('to_status', $filters['to_status']))
            ->when(!empty($filters['created_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['created_from']))
            ->when(!empty($filters['created_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['created_to']))
            ->latest('id')
            ->paginate($perPage);
    }
}
