<?php

namespace App\Services\Contracts;

use App\Models\User;

/**
 * Contrato para operacoes de autenticacao.
 */
interface AuthServiceInterface
{
    /**
     * Retorna usuario e token apos autenticar credenciais.
     */
    public function login(string $email, string $password): array;

    /**
     * Revoga token atual do usuario.
     */
    public function logout(User $user): void;
}
