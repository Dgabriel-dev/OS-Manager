# OS Assist - Sistema de Ordem de Servico

Sistema completo de gerenciamento de ordens de servico para assistencia tecnica, estoque e vendas.

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19-61DAFB?style=flat-square&logo=react)](https://react.dev)
[![TypeScript](https://img.shields.io/badge/TypeScript-6-3178C6?style=flat-square&logo=typescript)](https://typescriptlang.org)
[![Docker](https://img.shields.io/badge/Docker-24-2496ED?style=flat-square&logo=docker)](https://docker.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-17-4169E1?style=flat-square&logo=postgresql)](https://postgresql.org)
[![Redis](https://img.shields.io/badge/Redis-7-DC382D?style=flat-square&logo=redis)](https://redis.io)

---

## Funcionalidades

### Modulos

| Modulo | Descricao |
|--------|-----------|
| **Dashboard** | Visao geral com cards, graficos de receita/despesa, ultimas OS e estoque baixo |
| **Clientes** | Cadastro completo com busca, filtros, paginacao e restauracao |
| **Equipamentos** | Cadastro com arquivos, senhas criptografadas e associacao a clientes |
| **Ordens de Servico** | Fluxo completo com 6 status, historico, itens/servicos e download de PDFs |
| **Estoque** | Controle de entrada/saida com categorias, alerta de estoque baixo e movimentacoes |
| **Vendas** | Venda de produtos com categorias, custo por item e lucro por venda |
| **Financeiro** | Dashboard com receita vs despesa, lucro de vendas, grafico mensal (Recharts) |
| **Usuarios** | Sistema RBAC com 4 perfis de acesso e controle de permissoes |
| **Notificacoes** | Notificacoes internas com leitura individual e coletiva |
| **PDFs** | Geracao de OS, orcamento, recibo, garantia e laudo tecnico (DomPDF) |
| **Auditoria** | Log completo de todas as alteracoes criadas, atualizadas e removidas |

### Fluxo da Ordem de Servico

```
Aberta (open) -> Em Andamento (in_progress) -> Concluida (completed) -> Entregue (delivered)
                   |
                   +-> Aguardando Pecas (waiting_parts) -> Em Andamento
                   |
                   +-> Cancelada (cancelled)
```

### Status de Pagamento (Vendas)

```
Pendente (pending) -> Pago (paid)
Pendente (pending) -> Cancelado (cancelled)
```

### Perfis de Acesso (RBAC)

| Perfil | Permissoes |
|--------|-----------|
| **Administrador** | Acesso total ao sistema |
| **Atendente** | Clientes, Equipamentos, Ordens de Servico |
| **Tecnico** | Visualizar OS atribuidas, alterar status, laudos |
| **Financeiro** | Transacoes, relatorios, dashboard financeiro |

---

## Arquitetura

### Backend (Clean Architecture)

```
backend/
  app/
    Http/
      Controllers/Api/     # Controllers REST (thin controllers)
      Middleware/           # AuditMiddleware, EnsureUserIsActive
      Requests/            # Form Requests com validacao (17 Form Requests)
      Resources/           # API Resources para responses (20 Resources)
    Services/              # Logica de negocio (12 Services)
    Repositories/          # Repository Pattern (11 implementations)
      Contracts/           # Interfaces para DI (11 interfaces)
    Models/                # Eloquent Models (20 Models)
    Policies/              # Authorization Policies (6 Policies)
    Exceptions/            # Excecoes customizadas
    Providers/             # Service Providers (Repository bindings)
  database/
    migrations/            # 31 migrations
    seeders/               # 8 seeders (roles, permissoes, dados iniciais)
  routes/
    api.php                # 58 rotas REST protegidas
  config/                  # 11 arquivos de configuracao
```

### Frontend (Component Architecture)

```
frontend/src/
  api/                     # 11 clientes API tipados com Axios
  components/ui/           # 21 componentes reutilizaveis (shadcn/ui style)
  contexts/                # AuthContext, ThemeContext
  hooks/                   # useAuth, useDebounce, usePagination, useTheme
  layouts/                 # AuthLayout, DashboardLayout
  pages/                   # 23 paginas organizadas por modulo
  routes/                  # React Router com auth guards (37 rotas)
  types/                   # TypeScript interfaces
  utils/                   # formatters, validators (Zod), cn helper
```

### Padroes Arquiteturais

| Padrao | Aplicacao |
|--------|-----------|
| SOLID | Principios aplicados em todas as camadas |
| Clean Architecture | Separacao clara de responsabilidades |
| Repository Pattern | Abstracao de acesso a dados via Contracts |
| Service Layer | Toda logica de negocio nos Services |
| Dependency Injection | Repositories vinculados via RepositoryServiceProvider |
| Form Requests | Validacao isolada dos Controllers |
| API Resources | Transformacao consistente das responses |
| Policies | Controle de autorizacao granular por perfil |

---

## Endpoints da API (58 rotas)

### Autenticacao
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| POST | /api/login | Login (rate limit: 10/min) |
| POST | /api/logout | Logout |
| GET | /api/me | Perfil do usuario logado |
| PUT | /api/profile | Atualizar perfil |
| PUT | /api/password | Alterar senha |

### Clientes
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| GET | /api/clients | Listar clientes |
| POST | /api/clients | Criar cliente |
| GET | /api/clients/{id} | Detalhes do cliente |
| PUT | /api/clients/{id} | Atualizar cliente |
| DELETE | /api/clients/{id} | Remover cliente |
| POST | /api/clients/{id}/restore | Restaurar cliente |

### Equipamentos
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| GET | /api/equipment | Listar equipamentos |
| POST | /api/equipment | Criar equipamento |
| GET | /api/equipment/{id} | Detalhes do equipamento |
| PUT | /api/equipment/{id} | Atualizar equipamento |
| DELETE | /api/equipment/{id} | Remover equipamento |
| POST | /api/equipment/{id}/restore | Restaurar equipamento |
| GET | /api/clients/{id}/equipment | Equipamentos por cliente |
| POST | /api/equipment/{id}/files | Enviar arquivo |
| DELETE | /api/equipment/{id}/files/{fileId} | Remover arquivo |

### Ordens de Servico
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| GET | /api/service-orders | Listar OS |
| POST | /api/service-orders | Criar OS |
| GET | /api/service-orders/{id} | Detalhes da OS |
| PUT | /api/service-orders/{id} | Atualizar OS |
| DELETE | /api/service-orders/{id} | Remover OS |
| POST | /api/service-orders/{id}/restore | Restaurar OS |
| PUT | /api/service-orders/{id}/status | Alterar status |
| GET | /api/service-orders/{id}/history | Historico da OS |
| POST | /api/service-orders/{id}/items | Adicionar item |
| DELETE | /api/service-orders/{id}/items/{itemId} | Remover item |

### Estoque
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| GET | /api/stock/items | Listar itens |
| POST | /api/stock/items | Criar item |
| GET | /api/stock/items/{id} | Detalhes do item |
| PUT | /api/stock/items/{id} | Atualizar item |
| DELETE | /api/stock/items/{id} | Remover item |
| PUT | /api/stock/items/{id}/adjust | Ajustar estoque |
| GET | /api/stock/movements | Movimentacoes |
| GET | /api/stock/categories | Categorias de estoque |

### Vendas
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| GET | /api/sales | Listar vendas |
| POST | /api/sales | Criar venda |
| GET | /api/sales/{id} | Detalhes da venda |
| PUT | /api/sales/{id} | Atualizar venda |
| DELETE | /api/sales/{id} | Remover venda |
| PUT | /api/sales/{id}/status | Alterar status pagamento |
| GET | /api/sales/categories | Categorias de venda |
| POST | /api/sales/categories | Criar categoria de venda |

### Financeiro
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| GET | /api/financial/transactions | Listar transacoes |
| POST | /api/financial/transactions | Criar transacao |
| GET | /api/financial/transactions/{id} | Detalhes da transacao |
| PUT | /api/financial/transactions/{id} | Atualizar transacao |
| DELETE | /api/financial/transactions/{id} | Remover transacao |
| GET | /api/financial/dashboard | Dashboard financeiro |
| GET | /api/financial/revenue-by-month | Receita mensal (12 meses) |
| GET | /api/financial/categories | Categorias financeiras |

### Outros
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| GET | /api/dashboard | Dashboard principal |
| GET | /api/users | Listar usuarios |
| GET | /api/roles | Listar perfis |
| GET | /api/notifications | Listar notificacoes |
| PUT | /api/notifications/{id}/read | Marcar como lida |
| PUT | /api/notifications/read-all | Marcar todas como lidas |
| GET | /api/notifications/unread-count | Contagem de nao lidas |
| GET | /api/pdf/service-order/{id} | PDF da OS |
| GET | /api/pdf/budget/{id} | PDF do orcamento |
| GET | /api/pdf/receipt/{id} | PDF do recibo |
| GET | /api/pdf/warranty/{id} | PDF da garantia |
| GET | /api/pdf/technical-report/{id} | PDF do laudo tecnico |

---

## Banco de Dados

### Modelo Relacional (31 tabelas)

```
roles ──────────────> users
                       |
                       ├──> service_orders (tecnico)
                       ├──> service_orders (criador)
                       ├──> sales (vendedor)
                       ├──> notifications
                       ├──> audits
                       └──> stock_movements

clients ────────────> equipments
                       |
                       ├──> service_orders
                       └──> sales (opcional)

equipments ──────────> equipment_files
                       └──> service_orders

service_orders ──────> order_items
                       ├──> order_histories
                       ├──> transactions
                       └──> stock_movements

stock_categories ────> stock_items
                       └──> stock_movements

financial_categories > transactions

sale_categories ─────> sale_items
                       └──> sales
```

### Total de Migracoes: 31

---

## Instalacao

### Pre-requisitos

- Docker e Docker Compose v2+
- Git
- Node.js 18+

### Passo 1: Clone o repositorio

```bash
git clone https://github.com/Dgabriel-dev/OS-Manager.git
cd OS-Manager
```

### Passo 2: Inicie os containers

```bash
# Build e subir todos os containers
docker compose build
docker compose up -d

# Aguardar PostgreSQL inicializar
sleep 5

# Instalar dependencias PHP
docker compose exec php composer install

# Gerar Application Key
docker compose exec php php artisan key:generate

# Rodar migrations + seeders
docker compose exec php php artisan migrate --seed --force

# Criar link de storage
docker compose exec php php artisan storage:link
```

### Passo 3: Iniciar o frontend

```bash
cd frontend
npm install
npm run dev
```

### Passo 4: Acesse o sistema

| Servico | URL |
|---------|-----|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8080/api |
| MailHog | http://localhost:8025 |

### Credenciais padrao

```
Email: admin@osassist.com.br
Senha: password
```

---

## Docker

### Containers

| Servico | Container | Porta | Imagem |
|---------|-----------|-------|--------|
| Nginx | os-nginx | 8080 | nginx:1.27-alpine |
| PHP-FPM | os-php-fpm | 9000 | php:8.4-fpm-bookworm |
| PostgreSQL | os-postgres | 5432 | postgres:17-alpine |
| Redis | os-redis | 6379 | redis:7-alpine |
| MailHog | os-mailhog | 8025/1025 | mailhog/mailhog:latest |

### Comandos uteis

```bash
# Subir containers
docker compose up -d

# Parar containers
docker compose down

# Ver logs
docker compose logs -f php

# Executar artisan
docker compose exec php php artisan <comando>

# Rodar migrate:fresh com seed
docker compose exec php php artisan migrate:fresh --seed --force

# Rodar testes
docker compose exec php php artisan test
```

---

## Tecnologias

### Backend

| Tecnologia | Versao | Uso |
|------------|--------|-----|
| PHP | 8.4 | Linguagem principal |
| Laravel | 13 | Framework |
| PostgreSQL | 17 | Banco de dados |
| Redis | 7 | Cache e sessoes |
| Sanctum | 4 | Autenticacao API |
| DomPDF | 3 | Geracao de PDFs |
| Pest | 4 | Testes |

### Frontend

| Tecnologia | Versao | Uso |
|------------|--------|-----|
| React | 19 | UI Library |
| TypeScript | 6 | Tipagem estatica |
| Vite | 8 | Build tool |
| Tailwind CSS | 4 | Estilizacao |
| shadcn/ui | - | Componentes UI |
| React Router | 7 | Rotas SPA |
| React Hook Form | 7 | Formularios |
| Zod | 3 | Validacao |
| TanStack Query | 5 | State management |
| Recharts | 3 | Graficos |
| Axios | 1 | HTTP client |
| Lucide | - | Icones |

### Infraestrutura

| Tecnologia | Versao | Uso |
|------------|--------|-----|
| Docker | 24 | Containerizacao |
| Nginx | 1.27 | Web server |
| PHP-FPM | 8.4 | Runtime PHP |

---

## Estrutura de Pastas

```
OS-Manager/
  docker/
    nginx/               # Configuracoes Nginx + snippets
    php/                 # Dockerfile PHP-FPM + php.ini
    postgres/            # Script de inicializacao
  backend/               # Laravel 13 API
    api/                 # Entry point para Vercel (serverless)
    app/                 # Codigo da aplicacao (159 arquivos)
    config/              # 11 configuracoes
    database/            # 31 migrations, 8 seeders
    resources/views/pdf/ # 5 templates Blade para PDFs
    routes/              # Definicao de rotas
  frontend/              # React + TypeScript
    src/                 # Codigo fonte (72 arquivos)
    dist/                # Build de producao
  docker-compose.yml     # Orquestracao Docker
  README.md              # Esta documentacao
```

---

## Licenca

MIT License
