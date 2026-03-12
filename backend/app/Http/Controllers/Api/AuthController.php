<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthenticatedUserResource;
use App\Http\Resources\AuthLoginResource;
use App\Http\Resources\MessageResource;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;

/**
 * Controlador de autenticacao da API.
 */
class AuthController extends Controller
{
    /**
     * @param AuthServiceInterface $authService Servico de autenticacao.
     */
    public function __construct(private readonly AuthServiceInterface $authService)
    {
    }

    /**
     * Realiza login e emite token Sanctum.
     */
    public function login(LoginRequest $request): AuthLoginResource
    {
        $result = $this->authService->login(
            $request->validated('email'),
            $request->validated('password')
        );

        return (new AuthLoginResource([
            'token' => $result['token'],
            'user' => $result['user'],
        ]))->additional([
            'message' => 'Login successful.',
        ]);
    }

    /**
     * Revoga o token atual do usuario autenticado.
     */
    public function logout(Request $request): MessageResource
    {
        $this->authService->logout($request->user());

        return new MessageResource('Logout successful.');
    }

    /**
     * Retorna os dados do usuario autenticado.
     */
    public function me(Request $request): AuthenticatedUserResource
    {
        return (new AuthenticatedUserResource($request->user()))->additional([
            'message' => 'Authenticated user retrieved successfully.',
        ]);
    }
}
