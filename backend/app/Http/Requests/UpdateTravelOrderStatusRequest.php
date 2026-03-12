<?php

namespace App\Http\Requests;

use App\Enums\TravelOrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para atualizacao de status do pedido.
 */
class UpdateTravelOrderStatusRequest extends FormRequest
{
    /**
     * Autoriza validacao para usuarios autenticados.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras para status permitido na transicao.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                TravelOrderStatusEnum::Approved->value,
                TravelOrderStatusEnum::Cancelled->value,
            ])],
        ];
    }
}
