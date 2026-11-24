<?php

// app/Actions/Teams/CreateChurch.php

namespace App\Actions\Teams;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateChurch
{
    /**
     * Cria a igreja, vincula o usuário como owner e marca o onboarding
     * como concluído — tudo numa transação.
     */
    public function create(User $user, array $data): Team
    {
        return DB::transaction(function () use ($user, $data) {
            $team = Team::create([
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug($data['name']),
                'city' => $data['city'],
                'denomination' => $data['denomination'] ?? null,
                'onboarding_completed_at' => now(),
            ]);

            $team->members()->attach($user, [
                'role' => TeamRole::Owner->value,
            ]);

            $user->forceFill(['current_team_id' => $team->id])->save();

            return $team;
        });
    }

    /**
     * Gera slug único. Se "igreja-central" já existe, tenta
     * "igreja-central-2", depois "-3", e assim por diante.
     */
    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Team::withTrashed()->where('slug', $slug)->exists()) {
            $counter++;
            $slug = "{$base}-{$counter}";
        }

        return $slug;
    }
}
