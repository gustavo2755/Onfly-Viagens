<?php

namespace App\Services;

use App\Enums\TravelOrderStatusEnum;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\ConcurrentModificationException;
use App\Models\TravelOrder;
use App\Models\User;
use App\Services\Contracts\TravelOrderStatusServiceInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Servico de transicoes de status de pedidos.
 */
class TravelOrderStatusService implements TravelOrderStatusServiceInterface
{
    /**
     * Atualiza status respeitando regras de autorizacao e negocio.
     */
    public function updateStatus(User $adminUser, TravelOrder $travelOrder, string $status): TravelOrder
    {
        if (!$adminUser->isAdmin()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $nextStatus = TravelOrderStatusEnum::from($status);

        return DB::transaction(function () use ($travelOrder, $nextStatus) {
            $locked = $this->acquireLockOrFail($travelOrder->id);
            if ($locked->status !== TravelOrderStatusEnum::Requested) {
                throw new BusinessRuleException('O pedido já foi aprovado ou cancelado e não pode ser alterado.');
            }
            $locked->update(['status' => $nextStatus->value]);
            return $locked->fresh(['user']);
        });
    }

    /**
     * Obtem o pedido com lock (MySQL: FOR UPDATE NOWAIT).
     */
    private function acquireLockOrFail(int $travelOrderId): TravelOrder
    {
        $lockClause = config('database.default') === 'mysql' ? 'for update nowait' : null;

        if ($lockClause) {
            try {
                $locked = TravelOrder::where('id', $travelOrderId)
                    ->lock($lockClause)
                    ->firstOrFail();
            } catch (QueryException $e) {
                if ($this->isLockNowaitException($e)) {
                    $fresh = TravelOrder::with('user')->findOrFail($travelOrderId);
                    throw new ConcurrentModificationException(
                        'Outro administrador já está alterando ou alterou este pedido.',
                        $fresh
                    );
                }
                throw $e;
            }
            return $locked;
        }

        return TravelOrder::where('id', $travelOrderId)->firstOrFail();
    }

    /**
     * Verifica se a excecao indica falha de lock NOWAIT.
     */
    private function isLockNowaitException(QueryException $e): bool
    {
        $code = $e->getCode();
        $message = $e->getMessage();
        return $code === '3572'
            || str_contains($message, 'NOWAIT')
            || str_contains($message, 'Lock wait timeout')
            || str_contains($message, 'could not be acquired immediately');
    }
}
