<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    public function view(User $user, Schedule $schedule): bool
    {
        return $user->belongsToTeam($schedule->service->team);
    }

    public function create(User $user): bool
    {
        return true; // a checagem real está no Form Request, com o team da rota
    }

    public function update(User $user, Schedule $schedule): bool
    {
        return $user->can('manageSchedules', $schedule->service->team);
    }

    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->can('manageSchedules', $schedule->service->team);
    }
}
