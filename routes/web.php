<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Schedules\ScheduleController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

Route::middleware(['auth', 'team.member'])
    ->prefix('t/{team:slug}')
    ->group(function () {
        Route::resource('schedules', ScheduleController::class);
    });

require __DIR__.'/settings.php';

/*
|--------------------------------------------------------------------------
| Rotas geradas automaticamente pelo Route::resource('fotos', FotoController::class)
|--------------------------------------------------------------------------
|

| Verbo       | URI                 | Método  | Nome da Rota
| ------------|---------------------|---------|---------------
| GET         | /fotos              | index   | fotos.index
| GET         | /fotos/create       | create  | fotos.create
| POST        | /fotos              | store   | fotos.store
| GET         | /fotos/{foto}       | show    | fotos.show
| GET         | /fotos/{foto}/edit  | edit    | fotos.edit
| PUT/PATCH   | /fotos/{foto}       | update  | fotos.update
| DELETE      | /fotos/{foto}       | destroy | fotos.destroy
|
*/
