<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Travel Orders API',
    version: '1.0.0',
    description: 'API para gerenciamento de pedidos de viagem corporativa.'
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Servidor da API')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'apiKey',
    in: 'header',
    name: 'Authorization',
    description: 'Use: Bearer {token}'
)]
#[OA\Tag(name: 'Auth', description: 'Autenticacao')]
#[OA\Tag(name: 'Users', description: 'Usuarios')]
#[OA\Tag(name: 'TravelOrders', description: 'Pedidos de viagem')]
#[OA\Tag(name: 'Notifications', description: 'Notificacoes de alteracao de status')]
#[OA\Schema(
    schema: 'AuthenticatedUser',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@travelorders.test'),
        new OA\Property(property: 'role', type: 'string', example: 'admin'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'TravelOrder',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'requester_name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'destination', type: 'string', example: 'Lisbon'),
        new OA\Property(property: 'departure_date', type: 'string', format: 'date', example: '2026-03-15'),
        new OA\Property(property: 'return_date', type: 'string', format: 'date', example: '2026-03-22'),
        new OA\Property(property: 'departure_date_br', type: 'string', example: '15/03/2026', description: 'Data em formato BR dd/mm/yyyy'),
        new OA\Property(property: 'return_date_br', type: 'string', example: '22/03/2026', description: 'Data em formato BR dd/mm/yyyy'),
        new OA\Property(property: 'status', type: 'string', example: 'requested'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'TravelOrderStatusLog',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'travel_order_id', type: 'integer', example: 10),
        new OA\Property(property: 'admin_user_id', type: 'integer', example: 1),
        new OA\Property(property: 'from_status', type: 'string', example: 'requested'),
        new OA\Property(property: 'to_status', type: 'string', example: 'approved'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
class OpenApiSpec
{
    #[OA\Post(
        path: '/api/auth/login',
        tags: ['Auth'],
        summary: 'Autenticar usuario',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login realizado'),
            new OA\Response(response: 401, description: 'Credenciais invalidas'),
        ]
    )]
    public function login(): void {}

    #[OA\Post(
        path: '/api/auth/logout',
        tags: ['Auth'],
        summary: 'Logout do usuario autenticado',
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 200, description: 'Logout realizado')]
    )]
    public function logout(): void {}

    #[OA\Get(
        path: '/api/auth/me',
        tags: ['Auth'],
        summary: 'Dados do usuario autenticado',
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 200, description: 'Usuario autenticado')]
    )]
    public function me(): void {}

    #[OA\Get(
        path: '/api/users',
        tags: ['Users'],
        summary: 'Listar usuarios (admin)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Filtra por nome ou email (busca parcial)'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de usuarios'),
            new OA\Response(response: 403, description: 'Sem permissao'),
        ]
    )]
    public function listUsers(): void {}

    #[OA\Get(
        path: '/api/travel-orders',
        tags: ['TravelOrders'],
        summary: 'Listar pedidos de viagem',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'destination', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'requester_name', in: 'query', schema: new OA\Schema(type: 'string'), description: 'Busca parcial no nome do solicitante'),
            new OA\Parameter(name: 'user_id', in: 'query', schema: new OA\Schema(type: 'integer'), description: 'Admin only'),
            new OA\Parameter(name: 'departure_from', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'departure_to', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'Lista paginada')]
    )]
    public function listTravelOrders(): void {}

    #[OA\Post(
        path: '/api/travel-orders',
        tags: ['TravelOrders'],
        summary: 'Criar pedido de viagem',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['requester_name', 'destination', 'departure_date', 'return_date'],
                properties: [
                    new OA\Property(property: 'requester_name', type: 'string'),
                    new OA\Property(property: 'destination', type: 'string'),
                    new OA\Property(property: 'departure_date', type: 'string', format: 'date'),
                    new OA\Property(property: 'return_date', type: 'string', format: 'date'),
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: 'Pedido criado')]
    )]
    public function createTravelOrder(): void {}

    #[OA\Get(
        path: '/api/travel-orders/{id}',
        tags: ['TravelOrders'],
        summary: 'Detalhar pedido',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Detalhe do pedido'),
            new OA\Response(response: 404, description: 'Nao encontrado'),
        ]
    )]
    public function showTravelOrder(): void {}

    #[OA\Patch(
        path: '/api/travel-orders/{travelOrder}/status',
        tags: ['TravelOrders'],
        summary: 'Atualizar status do pedido (admin)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'travelOrder', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['approved', 'cancelled']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Status atualizado'),
            new OA\Response(response: 403, description: 'Sem permissao'),
            new OA\Response(response: 422, description: 'Regra de negocio violada'),
        ]
    )]
    public function updateTravelOrderStatus(): void {}

    #[OA\Get(
        path: '/api/travel-orders/dashboard',
        tags: ['TravelOrders'],
        summary: 'Resumo do dashboard',
        security: [['sanctum' => []]],
        responses: [new OA\Response(response: 200, description: 'Resumo de pedidos')]
    )]
    public function travelOrdersDashboard(): void {}

    #[OA\Get(
        path: '/api/travel-orders/status-logs',
        tags: ['TravelOrders'],
        summary: 'Listar logs de mudanca de status (admin)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de logs'),
            new OA\Response(response: 403, description: 'Sem permissao'),
        ]
    )]
    public function listTravelOrderStatusLogs(): void {}

    #[OA\Get(
        path: '/api/notifications',
        tags: ['Notifications'],
        summary: 'Listar notificacoes do usuario',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'unread_only', in: 'query', required: false, schema: new OA\Schema(type: 'integer', enum: [0, 1]), description: '1 para apenas nao lidas'),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de notificacoes'),
            new OA\Response(response: 401, description: 'Nao autenticado'),
        ]
    )]
    public function listNotifications(): void {}

    #[OA\Get(
        path: '/api/notifications/unread-count',
        tags: ['Notifications'],
        summary: 'Contagem de notificacoes nao lidas',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Contagem retornada'),
            new OA\Response(response: 401, description: 'Nao autenticado'),
        ]
    )]
    public function unreadCount(): void {}

    #[OA\Post(
        path: '/api/notifications/{id}/read',
        tags: ['Notifications'],
        summary: 'Marcar notificacao como lida',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'), description: 'UUID da notificacao'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Marcada como lida'),
            new OA\Response(response: 401, description: 'Nao autenticado'),
            new OA\Response(response: 404, description: 'Notificacao nao encontrada'),
        ]
    )]
    public function markNotificationAsRead(): void {}
}
