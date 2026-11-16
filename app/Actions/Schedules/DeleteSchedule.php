<?php

namespace App\Actions\Schedules;

use App\Models\Schedule;

class DeleteSchedule
{
    public function delete(Schedule $schedule): bool
    {
        return $schedule->delete();
    }
}