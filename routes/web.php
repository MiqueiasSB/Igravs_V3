<?php

use App\Http\Controllers\ChurchOnboardingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Schedules\ScheduleController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureChurchOnboarding;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas públicas
|--------------------------------------------------------------------------
| Qualquer pessoa acessa, logada ou não.
| A tela inicial é o ponto de entrada: criar igreja, tenho convite, login.
*/

Route::inertia('/', 'Welcome')->name('home');

/*
|--------------------------------------------------------------------------
| Onboarding — usuário logado, mas SEM igreja completa
|--------------------------------------------------------------------------
| Este grupo NÃO usa EnsureChurchOnboarding de propósito.
| Se usasse, o usuário seria redirecionado para cá em loop infinito.
|
| O middleware 'verified' garante que o e-mail foi confirmado antes de
| criar a igreja. Sem isso, qualquer um cria igreja com e-mail falso.
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('onboarding/igreja', [ChurchOnboardingController::class, 'create'])
        ->name('onboarding.church');

    Route::post('onboarding/igreja', [ChurchOnboardingController::class, 'store'])
        ->name('onboarding.church.store');
});

/*
|--------------------------------------------------------------------------
| Sistema — exige igreja completa e vínculo ativo
|--------------------------------------------------------------------------
| Prefixo: t/{current_team:slug}
|   - 'current_team' é o nome que o Jetstream e o SetTeamUrlDefaults esperam.
|   - ':slug' faz a URL usar o slug em vez do ID: /t/igreja-central/dashboard
|
| Middlewares, na ordem em que rodam:
|   1. auth                  → precisa estar logado
|   2. verified              → e-mail confirmado
|   3. EnsureTeamMembership  → precisa pertencer ao time da URL
|   4. EnsureChurchOnboarding→ cadastro da igreja precisa estar completo
|
| A ordem importa: o 3 resolve o time, o 4 verifica se ele está completo.
*/
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        $user = request()->user();

        if (! $user->currentTeam) {
            return redirect()->route('onboarding.church');
        }

        return redirect()->route('dashboard', [
            'current_team' => $user->currentTeam->slug,
        ]);
    })->name('dashboard.redirect');
});

Route::prefix('t/{current_team:slug}')
    ->middleware([
        'auth',
        'verified',
        EnsureTeamMembership::class,
        EnsureChurchOnboarding::class,
    ])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        /*
        | Route::resource gera as 7 rotas do CRUD de uma vez:
        |   GET    /schedules            index    schedules.index
        |   GET    /schedules/create     create   schedules.create
        |   POST   /schedules            store    schedules.store
        |   GET    /schedules/{id}       show     schedules.show
        |   GET    /schedules/{id}/edit  edit     schedules.edit
        |   PUT    /schedules/{id}       update   schedules.update
        |   DELETE /schedules/{id}       destroy  schedules.destroy
        */
        Route::resource('schedules', ScheduleController::class);

        // Próximos controllers seguem o mesmo padrão — descomente ao criar:
        // Route::resource('services', ServiceController::class);
        // Route::resource('people', PersonController::class);
    });

/*
|--------------------------------------------------------------------------
| Convites — não dependem de time ativo na URL
|--------------------------------------------------------------------------
| Quem aceita um convite ainda NÃO pertence ao time, então não pode passar
| por EnsureTeamMembership. Por isso este grupo fica fora do prefixo.
|
| O convite é identificado pelo token na URL, não pelo time da sessão.
*/

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])
        ->name('invitations.accept');

    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])
        ->name('invitations.decline');
});

/*
|--------------------------------------------------------------------------
| Rotas de settings (perfil, senha, aparência)
|--------------------------------------------------------------------------
| Vêm do Jetstream e já trazem seus próprios middlewares.
*/

require __DIR__.'/settings.php';
