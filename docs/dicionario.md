# Dicionário de dados — Igravs

Este documento descreve cada tabela do banco, o que ela representa, quem escreve
nela, quem lê e quais regras de negócio ela carrega. O diagrama visual fica em
`docs/database/database.svg`.

## teams

**O que é:** a igreja. É a raiz de todo o sistema — todo dado chega nela por
`team_id` direto ou por herança.

**Colunas principais:** id, name, slug, city, plan, trial_ends_at, is_personal,
created_at, updated_at, deleted_at

**Quem escreve:** Actions em `app/Actions/Teams` (CreateTeam, UpdateTeam,
DeleteTeam).

**Quem lê:** TeamController, middleware SetTeamUrlDefaults, dashboard.

**Regras:**

- `slug` é único e gerado automaticamente a partir do `name`.
- `is_personal` existe por herança do Jetstream e será removido: no Igravs toda
  igreja é real.
- Soft delete ativo: apagar uma igreja não apaga os dados em cascata
  imediatamente.

## users

**O que é:** a conta de login. Não é a pessoa escalada.

**Colunas principais:** id, name, email, password, current_team_id, created_at,
updated_at

**Quem escreve:** Actions em `app/Actions/Fortify` (CreateNewUser,
ResetUserPassword).

**Quem lê:** middleware HandleInertiaRequests, todas as telas autenticadas.

**Regras:**

- Um usuário pode participar de várias igrejas — o papel dele fica em
  `memberships`, não aqui.
- `current_team_id` guarda a igreja ativa no momento.

## memberships

**O que é:** a ponte entre usuário e igreja. Guarda o papel de acesso.

**Colunas principais:** id, team_id, user_id, role, created_at, updated_at

**Quem escreve:** Actions em `app/Actions/Teams` (AddTeamMember,
UpdateTeamMemberRole, RemoveTeamMember).

**Quem lê:** HasTeams trait, TeamPolicy, middleware EnsureTeamMembership.

**Regras:**

- `role` aceita apenas: owner, admin, leader, member (enum TeamRole).
- O mesmo usuário pode ser admin na igreja A e member na igreja B.
- Nunca confundir com `person_roles`: aqui é permissão de acesso, não função
  musical.

## people

**O que é:** a pessoa que é escalada. Pode ter conta no sistema ou não.

**Colunas principais:** id, team_id, user_id (nullable), name, email, phone,
active, created_at, updated_at

**Quem escreve:** Actions em `app/Actions/People` (CreatePerson, UpdatePerson,
DeletePerson).

**Quem lê:** ScheduleController, telas de montagem de escala.

**Regras:**

- `user_id` é opcional de propósito: permite escalar quem não usa o sistema.
- Quando a pessoa cria conta depois, o `user_id` é vinculado.
- `active = false` esconde a pessoa das listas de escalação, sem apagar o
  histórico.

## person_roles

**O que é:** as funções musicais que uma pessoa sabe exercer.

**Colunas principais:** id, person_id, role, created_at, updated_at

**Quem escreve:** Actions em `app/Actions/People` (SyncPersonRoles).

**Quem lê:** telas de montagem de escala, filtros por função.

**Regras:**

- Uma pessoa pode ter várias funções.
- `role` é texto controlado por enum: vocal, violao, guitarra, baixo, bateria,
  teclado, som.
- Não confundir com `memberships.role`: aqui é função musical, não permissão.

## services

**O que é:** o culto. Um evento com data e hora dentro de uma igreja.

**Colunas principais:** id, team_id, date, time, type, notes, created_at,
updated_at

**Quem escreve:** Actions em `app/Actions/Services` (CreateService, UpdateService,
DeleteService).

**Quem lê:** ScheduleController, dashboard, tela pública da escala.

**Regras:**

- `type` identifica o tipo de culto: domingo_manha, domingo_noite, quarta,
  especial.
- Um culto pertence a exatamente uma igreja.

## schedules

**O que é:** a escala. Liga uma pessoa a um culto numa função. É o coração do
sistema.

**Colunas principais:** id, service_id, person_id, role, status, created_at,
updated_at

**Quem escreve:** Actions em `app/Actions/Schedules` (CreateSchedule,
UpdateSchedule, DeleteSchedule, ConfirmSchedule).

**Quem lê:** ScheduleController, tela pública da escala, dashboard.

**Regras:**

- NÃO tem `team_id` de propósito: a igreja é alcançada via `service_id`. Isso
  impede escalar pessoa de outra igreja.
- `status` aceita apenas: pending, confirmed, declined.
- Uma pessoa não pode aparecer duas vezes no mesmo culto — constraint única em
  (service_id, person_id).
- A validação de `person_id` sempre filtra por `team_id` no Form Request.

## setlists

**O que é:** o repertório do culto. As músicas em ordem.

**Colunas principais:** id, service_id, song, key, link, position, created_at,
updated_at

**Quem escreve:** Actions em `app/Actions/Setlists` (CreateSetlistItem,
ReorderSetlist, DeleteSetlistItem).

**Quem lê:** tela pública da escala, tela do culto.

**Regras:**

- `position` define a ordem das músicas no culto.
- `key` é o tom da música (ex: G, D, Am).
- `link` aponta para cifra ou vídeo de referência.