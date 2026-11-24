<?php

// app/Http/Controllers/ChurchOnboardingController.php

namespace App\Http\Controllers;

use App\Actions\Teams\CreateChurch;
use App\Http\Requests\Teams\StoreChurchRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChurchOnboardingController extends Controller
{
    /**
     * Mostra o formulário de dados da igreja.
     * Se o usuário já tem igreja completa, manda para o dashboard.
     */
    public function create(): Response|RedirectResponse
    {
        $user = request()->user();

        if ($user->currentTeam?->onboarding_completed_at) {
            return redirect()->route('dashboard', [
                'current_team' => $user->currentTeam->slug,
            ]);
        }

        return Inertia::render('auth/onboarding/Church', [
            'defaultName' => $user->name,
        ]);
    }

    /**
     * Cria a igreja, vincula o usuário como owner e redireciona
     * para o dashboard já com o slug na URL.
     */
    public function store(StoreChurchRequest $request, CreateChurch $action): RedirectResponse
    {
        $team = $action->create($request->user(), $request->validated());

        return redirect()
            ->route('dashboard', ['current_team' => $team->slug])
            ->with('success', 'Igreja criada com sucesso.');
    }
}
