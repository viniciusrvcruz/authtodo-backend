# AuthTodo Backend

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Pest](https://img.shields.io/badge/Pest-FF6B6B?style=for-the-badge&logo=php&logoColor=white)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

</div>

### **API RESTful para gerenciamento de tarefas com autenticação múltipla (OAuth + OTP)**
*Desenvolvida com Laravel 12, PHP 8.4 e PostgreSQL*

<br>

> 🔗 **Frontend:** [AuthTodo Frontend](https://github.com/viniciusrvcruz/authtodo-frontend)

## 🎯 Sobre o Projeto

AuthTodo Backend é uma API robusta que combina gerenciamento de tarefas com múltiplos métodos de autenticação. O projeto oferece autenticação via OAuth (Google e GitHub) e OTP (One-Time Password) por email, proporcionando flexibilidade e segurança aos usuários.

## 🚀 Tecnologias

### Core
- **PHP** 8.4
- **Laravel** 12
- **PostgreSQL** 17

### Principais Dependências
- **Laravel Sanctum** - Autenticação de API
- **Laravel Socialite** - Autenticação OAuth (Google, GitHub)
- **Spatie One-Time Passwords** - Sistema de OTP
- **Pest PHP** - Framework de testes

### Infraestrutura
- **Docker** & **Docker Compose** - Containerização
- **Queue Workers** - Processamento assíncrono de notificações

## ✨ Funcionalidades

### Autenticação
- ✅ Login via Google OAuth
- ✅ Login via GitHub OAuth
- ✅ Login via OTP (código de 6 dígitos enviado por email)
- ✅ Logout com invalidação de sessão
- ✅ Rate limiting em rotas de autenticação (5 requisições/minuto)

### Gerenciamento de Tarefas
- ✅ Criar tarefas
- ✅ Listar tarefas do usuário autenticado
- ✅ Visualizar detalhes de uma tarefa
- ✅ Atualizar tarefas
- ✅ Excluir tarefas
- ✅ Marcar tarefas como concluídas

### Gerenciamento de Usuário
- ✅ Atualizar informações do perfil

## 📁 Estrutura do Projeto

```
authtodo-backend/
├── app/
│   ├── Enums/
│   │   └── AuthProviderEnum.php          # Enum para provedores OAuth
│   ├── Exceptions/
│   │   └── InvalidOneTimePasswordException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LogoutController.php
│   │   │   │   ├── OtpAuthController.php
│   │   │   │   └── SocialAuthController.php
│   │   │   ├── TaskController.php
│   │   │   └── UserController.php
│   │   ├── Requests/
│   │   │   ├── SendOtpAuthRequest.php
│   │   │   ├── VerifyOtpAuthRequest.php
│   │   │   ├── TaskRequest.php
│   │   │   └── UpdateUserRequest.php
│   │   └── Resources/
│   │       └── TaskResource.php
│   ├── Models/
│   │   ├── Task.php
│   │   └── User.php
│   ├── Notifications/
│   │   └── CustomOneTimePasswordNotification.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── RouteServiceProvider.php
│   ├── Services/
│   │   └── Auth/
│   │       ├── OtpAuthService.php
│   │       └── SocialAuthService.php
│   └── ValueObjects/
│       └── Email.php
├── docker/
│   └── php/                              # Configurações Docker
├── tests/
│   ├── Feature/                          # Testes de integração
│   └── Unit/                             # Testes unitários
├── docker-compose.yml
└── README.md
```

### Descrição dos Componentes

**Enums**: Define constantes tipadas (ex: provedores de autenticação)

**Exceptions**: Exceções customizadas com respostas JSON padronizadas

**Controllers**: Gerenciam requisições HTTP e retornam respostas

**Requests**: Validação de dados de entrada (Form Requests)

**Resources**: Transformação de modelos em respostas JSON

**Models**: Representação das entidades do banco de dados

**Services**: Lógica de negócio complexa (autenticação, processamento)

**ValueObjects**: Objetos imutáveis que encapsulam validação (ex: Email)

**Providers**: Configuração de serviços e bindings do Laravel

## 📦 Requisitos

- Docker & Docker Compose
- Git

## 🔧 Instalação

```bash
# Clone o repositório
git clone https://github.com/viniciusrvcruz/authtodo-backend.git
cd authtodo-backend

# Copie o arquivo de ambiente
cp .env.example .env

# Suba os containers (o setup é automático)
docker compose up -d
```

O processo de instalação é automatizado após subir os containers:
- ✅ Instala as dependências do Composer
- ✅ Gera a chave da aplicação (APP_KEY)
- ✅ Executa as migrations do banco de dados
- ✅ Inicia o servidor da API
- ✅ Inicia o queue worker para processamento de emails

## ⚙️ Configuração

### Variáveis de Ambiente

Configure as seguintes variáveis no arquivo `.env`:

```env
# Aplicação
APP_NAME="AuthTodo"
APP_URL=http://localhost
FRONTEND_URL=http://localhost:3000

# Banco de Dados
DB_CONNECTION=pgsql
DB_HOST=authtodo_postgresql
DB_PORT=5432
DB_DATABASE=authtodo_db
DB_USERNAME=postgres
DB_PASSWORD=password

# Email (para OTP)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@authtodo.com"
MAIL_FROM_NAME="${APP_NAME}"

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# GitHub OAuth
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URI="${APP_URL}/auth/github/callback"

# Queue
QUEUE_CONNECTION=database
```

## 🎮 Uso

### Iniciando o Projeto

```bash
docker compose up -d
```

A API estará disponível em `http://localhost`.

### Executando Comandos Artisan

```bash
docker exec -it authtodo_api php artisan <comando>
```

## 🧪 Testes

O projeto utiliza **Pest PHP** para testes.

```bash
# Executar todos os testes
docker exec -it authtodo_api php artisan test

# Executar testes específicos
docker exec -it authtodo_api php artisan test --filter=OtpAuthTest
```

### Cobertura de Testes

- ✅ Autenticação OAuth (Google, GitHub)
- ✅ Autenticação OTP (envio e verificação)
- ✅ Rate limiting
- ✅ CRUD de tarefas
- ✅ Atualização de usuário
- ✅ Autorização e segurança

## 🏗️ Arquitetura e Padrões

### Padrões Adotados

**Service Layer Pattern**: Lógica de negócio isolada em classes de serviço (OtpAuthService, SocialAuthService)

**Value Objects**: Validação encapsulada (Email)

**Form Request Validation**: Validação de entrada separada dos controllers

**API Resources**: Transformação consistente de dados

**Enum Types**: Constantes tipadas para maior segurança

### Boas Práticas

- ✅ Uso de UUIDs para IDs de recursos
- ✅ Prevenção de Lazy Loading em desenvolvimento
- ✅ Rate limiting em rotas sensíveis
- ✅ Validação de dados em todas as entradas
- ✅ Tratamento de exceções customizado
- ✅ Queue para processamento assíncrono de emails
- ✅ Route Model Binding com autorização automática
- ✅ Testes automatizados com alta cobertura
- ✅ Regeneração de sessão após login/logout

### Segurança

- Autenticação via Laravel Sanctum
- CSRF protection
- Rate limiting
- Validação de OTP com expiração
- Prevenção de reutilização de OTP
- Autorização em nível de recurso (usuário só acessa suas próprias tarefas)

## 📡 API Endpoints

### Autenticação

```
POST   /api/auth/otp/send              # Enviar OTP por email
POST   /api/auth/otp/verify            # Verificar OTP e fazer login
GET    /auth/{provider}/redirect       # Redirecionar para OAuth (google|github)
GET    /auth/{provider}/callback       # Callback OAuth
POST   /api/auth/logout                # Logout (autenticado)
GET    /api/user                        # Obter usuário autenticado
```

### Tarefas (Autenticado)

```
GET    /api/tasks                       # Listar tarefas
POST   /api/tasks                       # Criar tarefa
GET    /api/tasks/{task}                # Visualizar tarefa
PUT    /api/tasks/{task}                # Atualizar tarefa
DELETE /api/tasks/{task}                # Excluir tarefa
```

### Usuário (Autenticado)

```
PUT    /api/user/update                 # Atualizar perfil
```

### Exemplos de Requisições

**Enviar OTP:**
```bash
curl -X POST http://localhost/api/auth/otp/send \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

**Criar Tarefa:**
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Minha tarefa",
    "description": "Descrição da tarefa",
    "is_completed": false
  }'
```

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](https://opensource.org/licenses/MIT) para mais detalhes.

---

Desenvolvido com ❤️ por Vinicius Cruz
