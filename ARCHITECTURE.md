# 🏗️ Arquitetura do Thaluz

O Thaluz utiliza o padrão **MVC (Model-View-Controller)** adaptado para APIs modernas.

## 📂 Estrutura de Pastas

| Pasta | Descrição |
| :--- | :--- |
| **`app/`** | **Camada da Aplicação.** Lógica de negócio (Controllers e Models). |
| **`core/`** | **O Motor do Framework.** Contém Router, Request, Response e Database. |
| **`database/`** | **Banco de Dados.** Contém as migrations. |
| **`public/`** | **Ponto de Entrada.** Única pasta exposta ao servidor (`index.php`). |
| **`routes/`** | **Definição de Rotas.** Onde os endpoints são registrados. |
| **`artisan`** | **CLI do Framework.** Ferramenta de linha de comando para automação. |

## 🛠️ Funcionalidades Implementadas

### 1. Artisan CLI
Ferramenta inspirada no Laravel para automação de tarefas:
- **Geradores**: Criação automática de Controllers, Models e Migrations com templates pré-definidos.
- **Migrator**: Execução simplificada de scripts de banco de dados.

### 2. Camada de Dados (PDO)
- **Segurança**: Uso de *Prepared Statements* para prevenir SQL Injection.
- **Singleton**: Conexão única por requisição via `Core\Database`.

### 3. Roteamento e Respostas
- **Rotas Dinâmicas**: Suporte a parâmetros como `/users/{id}`.
- **Padronização**: Erros seguem o **RFC 7808** e sucessos retornam um objeto estruturado.

---

## 🔄 Fluxo de uma Requisição

1. O **Router** analisa a URI e o método HTTP.
2. O **Controller** correspondente é instanciado.
3. O **Model** interage com o banco via **PDO**.
4. O **Response** envia o JSON formatado de volta ao cliente.
