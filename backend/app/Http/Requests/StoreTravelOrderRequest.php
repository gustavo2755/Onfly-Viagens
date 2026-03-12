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
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'requester_name.required' => 'O nome do solicitante é obrigatório.',
            'requester_name.min' => 'O nome do solicitante deve ter pelo menos 3 caracteres.',
            'requester_name.max' => 'O nome do solicitante deve ter no máximo 120 caracteres.',
            'destination.required' => 'O destino é obrigatório.',
            'destination.min' => 'O destino deve ter pelo menos 2 caracteres.',
            'destination.max' => 'O destino deve ter no máximo 120 caracteres.',
            'departure_date.required' => 'A data de saída é obrigatória.',
            'departure_date.date' => 'A data de saída deve ser uma data válida.',
            'departure_date.after_or_equal' => 'A data de saída deve ser igual ou posterior a hoje.',
            'return_date.required' => 'A data de retorno é obrigatória.',
            'return_date.date' => 'A data de retorno deve ser uma data válida.',
            'return_date.after_or_equal' => 'A data de retorno deve ser igual ou posterior à data de saída.',
        ];
    }
}
