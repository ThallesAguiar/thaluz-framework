# Database Guide

Este guia descreve o suporte a multiplos bancos no thaluz, o padrao de
nomes das conexoes e como funciona o comando de clonagem.

## Visao geral

- O arquivo principal de configuracao e `config/database.php`.
- A conexao padrao vem de `DB_CONNECTION` (ex: `mysql`).
- As conexoes sao sempre identificadas em minusculo.
- O core escolhe o driver a partir de `driver` em cada conexao.

Drivers suportados:
`mysql`, `mariadb`, `pgsql`, `sqlite`, `sqlsrv`, `oracle`.

## Padronizacao de nomes

- As chaves em `connections` devem ser sempre minusculas.
- `DB_CONNECTION` deve usar o mesmo nome em minusculo.
- Para clones, o prefixo de env e sempre `DB_<NOME>_` com `<NOME>` em maiusculo.
  Exemplo de conexao `relatorios`:
  `DB_RELATORIOS_HOST`, `DB_RELATORIOS_DATABASE`, etc.

## Estrutura do config/database.php

Campos principais:

- `default`: nome da conexao padrao.
- `connections`: mapa de conexoes (chaves em minusculo).
- `migrations` e `seeders`: caminhos internos.

### MySQL / MariaDB

Chaves esperadas:

- `driver`: `mysql` ou `mariadb`
- `host`, `port`, `database`, `username`, `password`
- `charset`, `collation`

### PostgreSQL

Chaves esperadas:

- `driver`: `pgsql`
- `host`, `port`, `database`, `username`, `password`
- `charset`

### SQLite

Chaves esperadas:

- `driver`: `sqlite`
- `database`: caminho do arquivo (ex: `database/database.sqlite`)

Suporta `:memory:` para banco em memoria.

### SQL Server

Chaves esperadas:

- `driver`: `sqlsrv`
- `host`, `port`, `database`, `username`, `password`
- `charset`

### Oracle

Chaves esperadas:

- `driver`: `oracle`
- `host`, `port`, `database`, `username`, `password`
- `charset`

## Como o core conecta

Arquivo: `core/Database.php`.

Metodo principal:

`Database::getConnection(?string $connectionName = null)`

Fluxo:

1. Carrega `config/database.php`.
2. Define a conexao ativa:
   - se `$connectionName` for passado, usa ele
   - senao usa `DB_CONNECTION`
3. Normaliza o nome para minusculo.
4. Monta o DSN a partir do `driver`.
5. Cria e cacheia uma instancia de `PDO` por conexao.

DSNs usados:

- MySQL/MariaDB:
  `mysql:host=<host>;port=<port>;dbname=<db>;charset=<charset>`
- PostgreSQL:
  `pgsql:host=<host>;port=<port>;dbname=<db>`
- SQLite:
  `sqlite:<path>`
- SQL Server:
  `sqlsrv:Server=<host>,<port>;Database=<db>`
- Oracle (OCI):
  `oci:dbname=//<host>:<port>/<db>;charset=<charset>`

Resolucao de caminho SQLite:

- `:memory:` e aceito.
- Caminhos relativos sao resolvidos a partir da raiz do projeto.

## Comando de clonagem

Comando:

`php artisan make:db-connection-clone --from=mysql --to=relatorios`

O que ele faz:

1. Encontra a conexao `from` em `config/database.php`.
2. Duplica o bloco, trocando o nome da chave para `to`.
3. Atualiza as variaveis de ambiente dentro do bloco:
   - `DB_*` vira `DB_<TO>_*`
4. Adiciona as novas variaveis no `.env.example`.

Opcoes:

- `--dry-run`: nao grava arquivos.
- `--force`: sobrescreve se a conexao destino ja existir.

Regras de nomes:

- O comando sempre grava `from` e `to` em minusculo.
- Prefixos de env sao sempre em maiusculo.

## Manutencao futura

### Adicionar um novo driver base

1. Adicione a conexao em `config/database.php` com chave em minusculo.
2. Coloque um bloco correspondente em `.env.example` com comentario.
3. Se precisar de DSN customizado, ajuste `buildDsn()` em `core/Database.php`.

### Dicas

- Garanta que as extensoes PHP do driver estejam instaladas:
  - `pdo_mysql`, `pdo_pgsql`, `pdo_sqlite`, `pdo_sqlsrv`, `oci8`.
- Para Oracle e SQL Server, o host e o port devem ser validos
  para o servidor em uso.
