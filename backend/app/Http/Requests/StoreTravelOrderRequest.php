<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para criacao de pedido de viagem.
 */
class StoreTravelOrderRequest extends FormRequest
{
    /**
     * Autoriza criacao de pedido para usuario autenticado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validacao de criacao de pedido.
     */
    public function rules(): array
    {
        return [
            'requester_name' => ['required', 'string', 'min:3', 'max:120'],
            'destination' => ['required', 'string', 'min:2', 'max:120'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
        ];
    }
}
