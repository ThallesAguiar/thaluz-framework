# Arquitetura do thaluz

O thaluz usa um MVC enxuto orientado a API.

## Estrutura

| Pasta | Papel |
| :--- | :--- |
| `app/` | Controllers, Models, Middlewares e Services da aplicacao |
| `core/` | Nucleo do framework (Router, Request, Response, Database) |
| `database/` | Migrations |
| `public/` | Entrada HTTP (`index.php`) |
| `routes/` | Definicao de endpoints |
| `artisan` | CLI para automacoes |

## Migrations e rollback

- Migrations podem retornar string SQL (compatibilidade) ou array com `up`/`down`.
- O rollback usa o `down` da migration para desfazer as ultimas execucoes.

## Fluxo da requisicao

1. `public/index.php` carrega autoload e env.
2. Aliases de middleware sao registrados no `Router`.
3. `routes/api.php` define rotas publicas e protegidas.
4. `Router::dispatch()` resolve rota e parametros.
5. Middlewares da rota/grupo executam antes do controller.
6. Controller usa Models/Services e retorna JSON padronizado.

## JWT stateless (auth atual)

- `JwtService` cria e valida JWT.
- `access_token` usa `type=access`, `iss` e TTL curto.
- `refresh_token` usa `type=refresh`, `iss` e TTL maior.
- Middleware `auth` valida assinatura, tipo e expiracao do access token.
- Endpoint `/api/refresh` emite novo par de tokens sem estado no servidor.

## Consequencia do modo stateless

- Nao existe revogacao imediata no servidor.
- Logout depende do cliente descartar os tokens.
- Seguranca depende de TTL curto no access token e protecao do refresh token no cliente.

## Middleware e grupos

- Middlewares podem ser passados por rota.
- `Router::group(['middleware' => [...]])` aplica a varias rotas.
- Middlewares de grupo e de rota sao combinados automaticamente.

## Closures

- Rotas com Closure sao suportadas via `is_callable`.
- `Router::group(..., function () {})` usa Closure como callback.
