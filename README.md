# Thaluz - Mini Framework API PHP

Mini framework PHP inspirado no Laravel, focado em APIs.

## Requisitos

- PHP 8.0+
- Composer
- MySQL

## Como iniciar

1. Instale dependencias:

```bash
composer install
```

2. Configure o `.env` com banco e JWT:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=thaluz
DB_USERNAME=root
DB_PASSWORD=
JWT_SECRET=change_me_with_a_long_random_secret
JWT_ISSUER=Thaluz
JWT_ACCESS_TTL_MINUTES=15
JWT_REFRESH_TTL_DAYS=30
```

3. Rode migrations:

```bash
php artisan migrate
```

Rollback de migrations (exemplo, 2 passos):

```bash
php artisan migrate:rollback 2
```

4. Inicie o servidor:

```bash
composer dev
```

## Comandos Artisan

| Comando | Descricao |
| :--- | :--- |
| `php artisan migrate` | Executa migrations pendentes |
| `php artisan make:migration {nome}` | Cria migration |
| `php artisan make:controller {nome}` | Cria controller em `app/Controllers` |
| `php artisan make:model {nome}` | Cria model em `app/Models` |
| `php artisan make:middleware {nome}` | Cria middleware em `app/Middleware` |
| `php artisan rollback [steps]` | Desfaz as ultimas migrations |

## Autenticacao JWT 100% stateless

Fluxo atual:

1. `POST /api/login`
- valida email/senha
- retorna `access_token` (JWT curto)
- retorna `refresh_token` (JWT longo)

2. `GET /api/me` (protegida)
- exige `Authorization: Bearer <access_token>`
- middleware valida assinatura, `type=access` e expiracao

3. `POST /api/refresh`
- recebe `refresh_token`
- valida assinatura, `type=refresh` e expiracao
- retorna novo par de tokens JWT

4. `POST /api/logout`
- em modelo stateless, o backend apenas responde sucesso
- o cliente descarta os tokens localmente

## Aviso importante

Sem estado no servidor, nao existe revogacao imediata de token.
Um token so deixa de valer quando expira.

## Endpoints

### Publicos

- `GET /api`
- `GET /api/ping`
- `POST /api/users`
- `POST /api/login`
- `POST /api/refresh`

### Protegidos (middleware `auth`)

- `GET /api/me`
- `POST /api/logout`
- `GET /api/users`
- `GET /api/users/{id}`
- `PUT /api/users/{id}`
- `DELETE /api/users/{id}`
- `GET /api/project`

## Middleware e grupos

- Middleware por rota:

```php
Router::get('/api/users', 'UserController@index', ['auth']);
```

- Grupo de middleware:

```php
Router::group(['middleware' => ['auth']], function () {
    Router::get('/api/me', 'AuthController@me');
    Router::post('/api/logout', 'AuthController@logout');
});
```

## Closure

Sim, o projeto suporta Closure em handlers e em `Router::group(...)`.

Desenvolvido por Thalles Aguiar.
