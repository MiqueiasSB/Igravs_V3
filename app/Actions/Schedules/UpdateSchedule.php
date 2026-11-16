<?php

namespace App\Actions\Schedules;

use App\Models\Schedule;

class UpdateSchedule
{
    public function update(Schedule $schedule, array $data): Schedule
    {
        $schedule->update([
            'role' => $data['role'],
            'status' => $data['status'],
        ]);

        return $schedule->fresh();
    }
}