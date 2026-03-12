# 🚀 Thaluz - Mini Framework API PHP

Um mini-framework PHP inspirado no Laravel, focado em APIs leves e rápidas para pequenos projetos, utilizando o padrão MVC.

## 📋 Pré-requisitos

- **PHP 8.0+**
- **Composer**

## 🛠️ Como Iniciar o Projeto

1. **Instalar Dependências e Autoload**:
   ```bash
   composer install
   ```

2. **Configurar Banco de Dados**:
   - Crie um banco chamado `thaluz` no seu MySQL.
   - Verifique as credenciais no arquivo `.env`.
   - Execute as migrations via terminal:
   ```bash
   php artisan migrate
   ```

3. **Iniciar o Servidor**:
   ```bash
   composer start
   ```
   *O servidor estará disponível em: **http://localhost:8080***

---

## ⌨️ Comandos Artisan (CLI)

O Thaluz possui um CLI próprio para agilizar o desenvolvimento:

| Comando | Descrição |
| :--- | :--- |
| `php artisan migrate` | Executa todas as migrations pendentes |
| `php artisan make:migration {nome}` | Cria um novo arquivo de migration |
| `php artisan make:controller {nome}` | Cria um novo Controller em `app/Controllers` |
| `php artisan make:model {nome}` | Cria um novo Model em `app/Models` |

---

## 🧪 Endpoints da API (CRUD de Usuários)

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `GET` | `/api/users` | Listar todos os usuários |
| `GET` | `/api/users/{id}` | Buscar um usuário por ID |
| `POST` | `/api/users` | Criar um novo usuário |
| `PUT` | `/api/users/{id}` | Atualizar um usuário |
| `DELETE` | `/api/users/{id}` | Deletar um usuário |

---
Desenvolvido por Thalles Aguiar.
