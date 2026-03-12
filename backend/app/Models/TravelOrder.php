<?php

namespace App\Models;

use App\Enums\TravelOrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de pedido de viagem corporativa.
 */
class TravelOrder extends Model
{
    /** @use HasFactory<\Database\Factories\TravelOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
    ];

    /**
     * Casts de tipos para campos de dominio.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'status' => TravelOrderStatusEnum::class,
        ];
    }

    /**
     * Relacao com usuario solicitante.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
