# OS Assist - Sistema de Ordem de Servico

Sistema profissional de gerenciamento de ordens de servico para assistencia tecnica e vendas.

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-18-61DAFB?style=flat-square&logo=react)](https://react.dev)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat-square&logo=typescript)](https://typescriptlang.org)
[![Docker](https://img.shields.io/badge/Docker-24-2496ED?style=flat-square&logo=docker)](https://docker.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-17-4169E1?style=flat-square&logo=postgresql)](https://postgresql.org)
[![Redis](https://img.shields.io/badge/Redis-7-DC382D?style=flat-square&logo=redis)](https://redis.io)

---

## Funcionalidades

### Modulos
- **Dashboard** - Visao geral com cards, graficos e ultimas ordens
- **Clientes** - Cadastro completo com busca, filtros e paginacao
- **Equipamentos** - Cadastro com fotos, arquivos e senhas criptografadas
- **Ordens de Servico** - Fluxo completo com 6 status e historico de alteracoes
- **Estoque** - Controle de entrada/saida com alerta de estoque baixo
- **Vendas** - Venda de produtos com categorias, custo e lucro
- **Financeiro** - Receitas, despesas, grafico mensal, lucro e relatorios
- **Usuarios** - Sistema RBAC com 4 perfis de acesso
- **Notificacoes** - Email e sistema interno
- **PDFs** - OS, orcamento, recibo, garantia e laudo tecnico
- **Auditoria** - Log completo de todas as alteracoes

### Perfis de Acesso (RBAC)

| Perfil | Permissoes |
|--------|-----------|
| Administrador | Acesso total ao sistema |
| Atendente | Clientes, Equipamentos, Ordens de Servico |
| Tecnico | Visualizar OS, alterar status, laudos, pecas |
| Financeiro | Pagamentos, relatorios, caixa |

---

## Arquitetura

### Backend (Clean Architecture)
```
backend/
  app/
    Http/
      Controllers/Api/     # Controllers REST (thin controllers)
      Middleware/           # Middleware de auditoria e active user
      Requests/            # Form Requests com validacao
      Resources/           # API Resources para responses
    Services/              # Logica de negocio (Service Layer)
    Repositories/          # Repository Pattern (Eloquent)
      Contracts/           # Interfaces para Dependency Injection
    Models/                # Eloquent Models com relationships
    Policies/              # Authorization Policies
  database/
    migrations/            # Migrations organizadas
    seeders/               # Seeders com dados iniciais
  routes/
    api.php                # Rotas REST protegidas
```

### Frontend (Component Architecture)
```
frontend/src/
  api/                     # Chamadas API tipadas com Axios
  components/ui/           # Componentes reutilizaveis (shadcn/ui style)
  contexts/                # AuthContext e ThemeContext
  hooks/                   # Custom React Hooks
  layouts/                 # AuthLayout e DashboardLayout
  pages/                   # Paginas organizadas por modulo
  routes/                  # React Router com guards
  types/                   # TypeScript interfaces
  utils/                   # Formatters, validators (Zod), helpers
```

### Padroes Arquiteturais
- **SOLID** - Principios aplicados em todas as camadas
- **Clean Architecture** - Separacao clara de responsabilidades
- **Repository Pattern** - Abstracao de acesso a dados
- **Service Layer** - Toda logica de negocio nos Services
- **Dependency Injection** - Repositories vinculados via ServiceProvider
- **Form Requests** - Validacao isolada dos Controllers
- **API Resources** - Transformacao consistente das responses
- **Policies** - Controle de autorizacao granular

---

## Seguranca

| Medida | Implementacao |
|--------|--------------|
| SQL Injection | Eloquent ORM + prepared statements |
| XSS | Validacao de inputs + escaping |
| CSRF | Laravel CSRF tokens |
| Session Fixation | Regenerate session on login |
| Password Hash | Bcrypt (via Hash::make) |
| Cookies | HttpOnly + Secure + SameSite=Strict |
| Rate Limiting | Laravel Rate Limiter |
| Validacao | Form Requests com rules rigorosas |
| Auditoria | Log completo de todas as alteracoes |
| RBAC | Controle por perfil de usuario |

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

### Passo 2: Inicie os containers e instale dependencias
```bash
# Build e subir todos os containers (PHP 8.4 + PostgreSQL + Redis + Nginx + MailHog)
docker compose build
docker compose up -d

# Aguardar PostgreSQL inicializar
sleep 5

# Instalar dependencias PHP
docker compose exec php composer install

# Gerar Application Key
docker compose exec php php artisan key:generate

# Rodar migrations + seeders (cria tabelas e dados iniciais)
docker compose exec php php artisan migrate --seed --force

# Criar link de storage
docker compose exec php php artisan storage:link
```

### Passo 3: Instalar e iniciar o frontend
```bash
cd frontend
npm install
npm run dev
```

### Passo 4: Acesse o sistema
- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8080/api
- **MailHog:** http://localhost:8025

### Credenciais padrao
```
Email: admin@osassist.com.br
Senha: password
```

---

## Docker

### Containers

| Servico | Container | Porta |
|---------|-----------|-------|
| Nginx | os-nginx | 8080 |
| PHP-FPM | os-php-fpm | 9000 |
| PostgreSQL | os-postgres | 5432 |
| Redis | os-redis | 6379 |
| MailHog | os-mailhog | 8025 |

### Comandos uteis
```bash
# Subir containers
docker compose up -d

# Parar containers
docker compose down

# Reconstruir container especifico
docker compose build --no-cache php

# Ver logs
docker compose logs -f php

# Executar artisan
docker compose exec php php artisan <comando>

# Rodar migrate:fresh com seed
docker compose exec php php artisan migrate:fresh --seed --force
```

---

## API Endpoints

### Autenticacao
| Metodo | Endpoint | Descricao |
|--------|----------|-----------|
| POST | /api/login | Login |
| POST | /api/logout | Logout |
| GET | /api/me | Perfil do usuario |
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

### Modelo Relacional
- **roles** -> **users** (1:N)
- **users** -> **clients** (1:N, criador)
- **clients** -> **equipments** (1:N)
- **users** -> **service_orders** (1:N, tecnico)
- **clients** -> **service_orders** (1:N)
- **equipments** -> **service_orders** (1:N)
- **service_orders** -> **order_items** (1:N)
- **service_orders** -> **order_histories** (1:N)
- **stock_categories** -> **stock_items** (1:N)
- **stock_items** -> **stock_movements** (1:N)
- **financial_categories** -> **transactions** (1:N)
- **service_orders** -> **transactions** (1:N)
- **sale_categories** -> **sale_items** (1:N)
- **sales** -> **sale_items** (1:N)
- **clients** -> **sales** (1:N, opcional)
- **users** -> **sales** (1:N, vendedor)
- **users** -> **notifications** (1:N)
- **users** -> **audits** (1:N)

---

## Estrutura de Pastas

```
OS-Manager/
  docker/
    nginx/               # Configuracoes Nginx
    php/                 # Dockerfile PHP-FPM
    postgres/            # Script de inicializacao
  backend/               # Laravel 13 API
    app/                 # Codigo da aplicacao
    config/              # Configuracoes
    database/            # Migrations, seeders
    routes/              # Definicao de rotas
  frontend/              # React + TypeScript
    src/                 # Codigo fonte
    dist/                # Build de producao
  docker-compose.yml     # Orquestracao Docker
  .env                   # Variaveis de ambiente Docker
  README.md              # Esta documentacao
```

---

## Tecnologias

### Backend
- PHP 8.4 | Laravel 13 | PostgreSQL 17 | Redis 7
- Laravel Sanctum | Eloquent ORM | DomPDF
- Repository Pattern | Service Layer

### Frontend
- React 18 | TypeScript | Vite
- Tailwind CSS 4 | shadcn/ui style
- React Router | React Hook Form | Zod
- TanStack Query | Axios | Recharts
- Lucide Icons | date-fns

### Infraestrutura
- Docker | Nginx | PHP-FPM | PostgreSQL | Redis

---

## Licenca

MIT License
