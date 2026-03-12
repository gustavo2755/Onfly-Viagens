<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request de autenticacao.
 */
class LoginRequest extends FormRequest
{
    /**
     * Autoriza requisicao de login.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validacao de login.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
