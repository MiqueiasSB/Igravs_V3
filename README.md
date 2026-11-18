# Igravs

Sistema de gestão de escalas de louvor para igrejas. Permite cadastrar pessoas,
cultos, funções musicais e montar a escala de cada culto, com confirmação de
presença pelo membro.

## Stack

- Laravel 13 (PHP 8.3)
- Inertia.js + Vue 3
- Jetstream (autenticação, times e convites)
- Tailwind CSS v4 + shadcn-vue
- MySQL 8
- Docker + Docker Compose
- Pest (testes)

## Arquitetura

O projeto segue o padrão do Jetstream: Controller → Action → Model.

- Controllers apenas orquestram: recebem o request, chamam uma Action e devolvem
  a resposta Inertia.
- Actions executam a regra de negócio. Cada arquivo faz uma operação atômica.
- Form Requests validam a entrada antes de chegar no controller.
- Policies decidem autorização.
- Models guardam relacionamentos e scopes de leitura.

Não existem pastas Services nem Repositories. O Eloquent já é a camada de acesso
a dados.

## Como rodar

Pré-requisitos: Docker e Docker Compose instalados.

1. Copiar o arquivo de ambiente:

    cp .env.example .env

2. Subir os containers:

    docker compose up -d

3. Instalar dependências PHP:

    docker compose exec app composer install

4. Gerar a chave da aplicação:

    docker compose exec app php artisan key:generate

5. Rodar as migrations e seeders:

    docker compose exec app php artisan migrate --seed

6. Instalar dependências do frontend e compilar:

    docker compose exec app npm install
    docker compose exec app npm run dev

A aplicação fica disponível em http://localhost:8000

## Comandos úteis

Todos os comandos do Artisan devem ser executados DENTRO do container, com o
prefixo `docker compose exec app`.

- Subir containers: `docker compose up -d`
- Ver status: `docker compose ps`
- Ver logs do banco: `docker compose logs mysql --tail=50`
- Abrir o console interativo: `docker compose exec app php artisan tinker`
- Rodar migrations: `docker compose exec app php artisan migrate`
- Recriar o banco: `docker compose exec app php artisan migrate:fresh --seed`
- Rodar os testes: `docker compose exec app php artisan test`
- Limpar cache de config: `docker compose exec app php artisan config:clear`
- Gerar o diagrama ER: `docker compose exec app php artisan generate:erd docs/database.svg`

## Documentação

- `docs/database/dicionario.md` — dicionário de dados: o que cada tabela
  representa, quem escreve, quem lê e as regras de negócio.
- `docs/database/database.svg` — diagrama ER gerado a partir do banco real.
- `docs/adr/` — registros de decisão de arquitetura.

## Estrutura de pastas

| Pasta                  | Responsabilidade                      |
| ---------------------- | ------------------------------------- |
| `app/Actions`          | Operações que gravam ou alteram dados |
| `app/Concerns`         | Traits compartilhadas                 |
| `app/Data`             | DTOs                                  |
| `app/Enums`            | Valores fixos tipados                 |
| `app/Http/Controllers` | Orquestração                          |
| `app/Http/Middleware`  | Filtros de requisição                 |
| `app/Http/Requests`    | Validação                             |
| `app/Http/Responses`   | Redirecionamento pós-login            |
| `app/Models`           | Eloquent                              |
| `app/Notifications`    | Avisos                                |
| `app/Policies`         | Autorização                           |
| `app/Providers`        | Bootstrap                             |
| `app/Rules`            | Regras de validação customizadas      |

## Convenções

- Todo dado do sistema pertence a uma igreja (`teams`). A igreja é a raiz.
- `schedules` não tem `team_id` de propósito: chega na igreja via `service_id`.
- `users` é a conta de login; `people` é a pessoa escalada. O vínculo entre elas
  é opcional.
- `memberships.role` é papel de acesso (admin, leader, member).
  `person_roles.role` é função musical (vocal, violão, som). Nunca misturar.
