<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureChurchOnboarding
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $team = $request->route('current_team');

        // A rota entrega a STRING do slug, não o Model
        if (is_string($team)) {
            $team = Team::where('slug', $team)->first();
        }

        if (! $team instanceof Team) {
            return $next($request);
        }

        if (! $team->onboarding_completed_at) {
            return redirect()->route('onboarding.church');
        }

        return $next($request);
    }
}
