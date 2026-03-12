# Travel Orders

Aplicacao Full Stack para gerenciamento de pedidos de viagem corporativa, organizada em duas etapas de desenvolvimento.

## Stack

- Back-end: Laravel 12, Sanctum, Notifications, Redis, PHPUnit (SQLite em testes)
- Controle de acesso: Spatie Laravel Permission (roles)
- Front-end: Vue 3, Vite, Pinia (etapa 2)
- Infra: Docker Compose com PHP 8.3, Nginx, MySQL, Redis e Node

## Arquitetura

- `backend/`: API REST API-first em Laravel
- `frontend/`: SPA Vue (estrutura pronta, implementacao na etapa 2)
- `docker/`: definicoes de containers
- `docker-compose.yml`: orquestracao unica do ecossistema

### Decisoes principais

- Autenticacao por token via Sanctum (opcao oficial Laravel para APIs e SPAs, aderente ao requisito de autenticacao simples por token).
- Autorizacao com `spatie/laravel-permission` usando apenas roles neste momento (`admin` e `user`), sem permissões granulares por enquanto.
- Controllers enxutos, regras de negocio em Services, validacao em Form Requests, serializacao em API Resources e autorizacao por Policy.
- Cache aplicado apenas em dashboard/listagem admin com invalidacao simples por versionamento de chave.

## Estrutura

```text
travel-orders/
├── backend/
├── frontend/
├── docker/
│   ├── frontend/
│   ├── mysql/
│   ├── nginx/
│   └── php/
├── docker-compose.yml
└── README.md
```

## Como executar com Docker

1. Entre na pasta raiz:
   - `cd travel-orders`
2. Copie o env do backend:
   - `cp backend/.env.example backend/.env`
3. Suba os containers:
   - `docker compose up -d --build`
4. Instale dependencias PHP dentro do container app (se necessario):
   - `docker compose exec app composer install`
5. Gere chave e rode migrations:
   - `docker compose exec app php artisan key:generate`
   - `docker compose exec app php artisan migrate --seed`

Back-end ficara disponivel em `http://localhost:8080`.

## Como rodar backend isolado (sem Docker)

1. `cd backend`
2. `cp .env.example .env`
3. `composer install`
4. `php artisan key:generate`
5. Ajuste banco no `.env`
6. `php artisan migrate --seed`
7. `php artisan serve`

## Como rodar frontend isolado

Etapa 2 ainda nao implementada. A pasta `frontend/` foi reservada para a segunda etapa.

## Variaveis de ambiente (backend)

Principais variaveis em `backend/.env`:

- `APP_URL` (ex: `http://localhost:8080`)
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `CACHE_STORE` (redis em producao, array em testes)
- `REDIS_HOST`, `REDIS_PORT`
- `L5_SWAGGER_CONST_HOST` (URL do servidor para Swagger UI, ex: `http://localhost:8080`)

## Migrations e Seeders

- Rodar migrations:
  - `php artisan migrate`
- Rodar seeders:
  - `php artisan db:seed`
- Reset completo:
  - `php artisan migrate:fresh --seed`

## Testes

Os testes usam **SQLite em memoria** (`:memory:`), configurado em `phpunit.xml`, garantindo banco novo a cada execucao.

- Executar localmente:
  - `cd backend && php artisan test`
- Executar dentro do Docker:
  - `docker compose exec app php artisan test`

O `phpunit.xml` define `DB_CONNECTION=sqlite` e `DB_DATABASE=:memory:` para testes, independente do `.env`.

## Swagger (L5-Swagger)

- Gerar documentacao OpenAPI:
  - `php artisan l5-swagger:generate`
- Abrir UI Swagger (requer role admin):
  - `http://localhost:8080/api/documentation`
  - Autenticacao: Bearer token no header ou `?token=SEU_TOKEN` na URL
- JSON gerado:
  - `backend/storage/api-docs/api-docs.json`

A especificacao esta em `backend/app/OpenApi/OpenApiSpec.php`.

## Credenciais seed

- Admin:
  - `email: admin@travelorders.test`
  - `password: password`
- Usuario comum:
  - `email: user@travelorders.test`
  - `password: password`

## Endpoints da API (etapa 1)

Autenticacao:

- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`

Pedidos:

- `GET /api/travel-orders`
- `POST /api/travel-orders`
- `GET /api/travel-orders/{id}`
- `PATCH /api/travel-orders/{id}/status`
- `GET /api/travel-orders/dashboard`
- `GET /api/travel-orders/status-logs` (admin)

## Fluxo por etapas

1. Etapa 1: Back-end completo (concluida nesta entrega).
2. Etapa 2: Front-end Vue completo (nao iniciada nesta entrega).
