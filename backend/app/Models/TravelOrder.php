<?php

namespace App\Models;

use App\Enums\TravelOrderStatusEnum;
use App\Traits\FormatsDateBr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de pedido de viagem corporativa.
 */
class TravelOrder extends Model
{
    use FormatsDateBr;
    use HasFactory;

    /**
     * Campos permitidos para atribuicao em massa.
     *
     * @var array<int, string>
     */
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
     * Accessor da data de saida no formato brasileiro.
     */
    public function getDepartureDateBrAttribute(): ?string
    {
        return $this->formatDateBr($this->departure_date);
    }

    /**
     * Accessor da data de retorno no formato brasileiro.
     */
    public function getReturnDateBrAttribute(): ?string
    {
        return $this->formatDateBr($this->return_date);
    }

    /**
     * Relacao com o usuario dono do pedido.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
