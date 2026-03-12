<?php

namespace App\Services;

use App\Models\TravelOrder;
use App\Models\User;
use App\Services\Contracts\TravelOrderQueryServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Servico de consultas, filtros e metadados de dashboard.
 */
class TravelOrderQueryService implements TravelOrderQueryServiceInterface
{
    /**
     * Lista pedidos com filtros e cache restrito ao contexto admin.
     */
    public function list(User $user, array $filters): LengthAwarePaginator
    {
        $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 50));
        $page = max(1, (int) ($filters['page'] ?? 1));
        $cacheable = $user->isAdmin();

        if (!$cacheable) {
            return $this->buildQuery($user, $filters)->paginate($perPage, ['*'], 'page', $page);
        }

        $key = $this->adminListCacheKey($filters, $perPage, $page);

        return Cache::remember($key, now()->addMinutes(2), function () use ($user, $filters, $perPage, $page) {
            return $this->buildQuery($user, $filters)->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /**
     * Busca um pedido visivel ao usuario atual.
     */
    public function findVisibleById(User $user, int $travelOrderId): ?TravelOrder
    {
        $query = TravelOrder::query()->with('user');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->find($travelOrderId);
    }

    /**
     * Retorna dados do dashboard com cache para admins.
     */
    public function dashboard(User $user): array
    {
        if (!$user->isAdmin()) {
            return $this->dashboardData($user);
        }

        $key = 'travel_orders_admin_dashboard:v'.$this->adminCacheVersion();

        return Cache::remember($key, now()->addMinutes(2), function () use ($user) {
            return $this->dashboardData($user);
        });
    }

    /**
     * Monta query base de listagem.
     */
    private function buildQuery(User $user, array $filters): Builder
    {
        return TravelOrder::query()
            ->with('user')
            ->when(!$user->isAdmin(), fn (Builder $query) => $query->where('user_id', $user->id))
            ->when(!empty($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(!empty($filters['destination']), fn (Builder $query) => $query->where('destination', 'like', '%'.$filters['destination'].'%'))
            ->when(!empty($filters['created_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['created_from']))
            ->when(!empty($filters['created_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['created_to']))
            ->when(!empty($filters['departure_from']), fn (Builder $query) => $query->whereDate('departure_date', '>=', $filters['departure_from']))
            ->when(!empty($filters['departure_to']), fn (Builder $query) => $query->whereDate('departure_date', '<=', $filters['departure_to']))
            ->orderByDesc('id');
    }

    /**
     * Gera contadores agregados do dashboard.
     */
    private function dashboardData(User $user): array
    {
        $query = TravelOrder::query();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return [
            'total' => (clone $query)->count(),
            'requested' => (clone $query)->where('status', 'requested')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Gera chave de cache da listagem admin.
     */
    private function adminListCacheKey(array $filters, int $perPage, int $page): string
    {
        ksort($filters);
        $payload = json_encode($filters);

        return 'travel_orders_admin_list:v'.$this->adminCacheVersion().':'.sha1($payload.':'.$perPage.':'.$page);
    }

    /**
     * Obtem versao de invalidação do cache admin.
     */
    private function adminCacheVersion(): int
    {
        return (int) Cache::get('travel_orders_admin_cache_version', 1);
    }
}
