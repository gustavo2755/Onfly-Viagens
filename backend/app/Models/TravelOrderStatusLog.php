<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de auditoria de mudancas de status de pedidos de viagem.
 */
class TravelOrderStatusLog extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para atribuicao em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'travel_order_id',
        'admin_user_id',
        'from_status',
        'to_status',
    ];

    /**
     * Relacao com o pedido de viagem alterado.
     */
    public function travelOrder(): BelongsTo
    {
        return $this->belongsTo(TravelOrder::class);
    }

    /**
     * Relacao com o administrador que executou a alteracao.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
