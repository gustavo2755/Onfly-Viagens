<?php

namespace App\Http\Requests;

use App\Enums\TravelOrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para filtros e paginacao de listagem de pedidos.
 */
class IndexTravelOrderRequest extends FormRequest
{
    /**
     * Autoriza listagem para usuario autenticado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validacao dos filtros de listagem.
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::enum(TravelOrderStatusEnum::class)],
            'destination' => ['nullable', 'string', 'min:2', 'max:120'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'departure_from' => ['nullable', 'date'],
            'departure_to' => ['nullable', 'date', 'after_or_equal:departure_from'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
