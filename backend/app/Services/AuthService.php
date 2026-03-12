<?php

namespace App\Services;

use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

/**
 * Servico de autenticacao via Sanctum.
 */
class AuthService implements AuthServiceInterface
{
    /**
     * Valida credenciais e retorna token com usuario autenticado.
     */
    public function login(string $email, string $password): array
    {
        if (!Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        $user = Auth::guard('web')->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Revoga o token de acesso atual.
     */
    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        Auth::guard('web')->logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }
}