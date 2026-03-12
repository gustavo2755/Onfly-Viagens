# Travel Orders API

## Swagger

- UI: `http://localhost:8080/api/documentation` (requer autenticacao admin)
- Acesso: Bearer token no header ou `?token=xxx` na URL
- JSON: `storage/api-docs/api-docs.json`
- Geracao: `php artisan l5-swagger:generate`

## Autenticacao

Token Bearer via Sanctum. Enviar header: `Authorization: Bearer {token}`.

- `POST /api/auth/login` - retorna `token` e `user`
- `POST /api/auth/logout` (auth)
- `GET /api/auth/me` (auth)

## Roles

- `admin`: pode listar todos os pedidos, aprovar/cancelar status, ver dashboard
- `user`: so ve e cria os proprios pedidos

## Usuarios (admin)

- `GET /api/users` (auth, admin) - Lista usuarios para filtro de pedidos
  - Query: `search` (opcional) - filtra por nome ou email (busca parcial, case-insensitive)

## Travel Orders

- `GET /api/travel-orders` (auth)
- `POST /api/travel-orders` (auth)
- `GET /api/travel-orders/{id}` (auth)
- `PATCH /api/travel-orders/{id}/status` (auth, admin)
- `GET /api/travel-orders/dashboard` (auth)
- `GET /api/travel-orders/status-logs` (auth, admin)

## Status do pedido

- `requested` - aguardando aprovacao
- `approved` - aprovado
- `cancelled` - cancelado

Pedido aprovado nao pode ser cancelado.

## Payloads

### POST /api/auth/login

```json
{
  "email": "user@travelorders.test",
  "password": "password"
}
```

Resposta 200: `{ "message": "...", "data": { "token": "...", "user": { "id", "name", "email", "role" } } }`

### POST /api/travel-orders

```json
{
  "requester_name": "John Doe",
  "destination": "Lisbon",
  "departure_date": "2026-03-15",
  "return_date": "2026-03-22"
}
```

Validacao: `requester_name` 3-120 chars, `destination` 2-120 chars, `return_date` >= `departure_date`.

### PATCH /api/travel-orders/{id}/status

```json
{
  "status": "approved"
}
```

Valores permitidos: `approved`, `cancelled`. Apenas admin.

## Filtros (GET /api/travel-orders)

- `status` - requested | approved | cancelled
- `destination` - string 2-120 chars
- `requester_name` - string 2-120 chars (busca parcial no nome do solicitante)
- `user_id` - ID do usuario (apenas admin, filtra pedidos do usuario)
- `departure_from`, `departure_to` - datas (departure_to >= departure_from)
- `page`, `per_page` - paginacao (per_page max 50)

## Formato de resposta

- Sucesso: `{ "message": "...", "data": ... }`
- Paginado: `{ "message": "...", "data": [...], "meta": { "current_page", "last_page", "per_page", "total" }, "links": {...} }`
- Validacao 422: `{ "message": "The given data was invalid.", "errors": { "campo": ["mensagem"] } }`
- Regra de negocio 422: `{ "message": "Approved travel orders cannot be cancelled." }`
- Nao autorizado 401: `{ "message": "Unauthenticated." }`
- Sem permissao 403: `{ "message": "This action is unauthorized." }`
- Nao encontrado 404: `{ "message": "No query results for model..." }`

## Auditoria de status (admin)

Endpoint: `GET /api/travel-orders/status-logs`

- Apenas admin pode consultar.
- Retorno paginado ordenado do mais recente para o mais antigo.

Item de `data`:

- `id`
- `travel_order_id`
- `admin_user_id`
- `from_status`
- `to_status`
- `created_at`
- `admin_user` (`id`, `name`, `email`, `role`)

## Estrutura do TravelOrder (data)

- `id`, `user_id`, `requester_name`, `destination`, `departure_date`, `return_date`, `status`, `created_at`, `updated_at`
- `departure_date_br`, `return_date_br` - datas em formato BR (dd/mm/yyyy) para exibicao
- No show: inclui `user` com `id`, `name`, `email`, `role`

## Dashboard (data)

- `total`, `requested`, `approved`, `cancelled` - contagens por status
