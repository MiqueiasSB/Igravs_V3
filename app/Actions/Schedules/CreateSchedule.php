<?php

namespace App\Actions\Schedules;

use App\Models\Schedule;
use App\Models\Service;

class CreateSchedule
{
    public function create(Service $service, array $data): Schedule
    {
        return $service->schedules()->create([
            'person_id' => $data['person_id'],
            'role' => $data['role'],
            'status' => $data['status'] ?? 'pending',
        ]);
    }
}
