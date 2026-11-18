<?php

// app/Http/Controllers/Schedules/ScheduleController.php

namespace App\Http\Controllers\Schedules;

use App\Actions\Schedules\CreateSchedule;
use App\Actions\Schedules\DeleteSchedule;
use App\Actions\Schedules\UpdateSchedule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Schedules\StoreScheduleRequest;
use App\Http\Requests\Schedules\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function index(Service $service): Response
    {
        return Inertia::render('Schedules/Index', [
            'service' => $service,
            'schedules' => $service->schedules()->with('person')->get(),
        ]);
    }

    public function store(StoreScheduleRequest $request, Service $service, CreateSchedule $action): RedirectResponse
    {
        $action->create($service, $request->validated());

        return back()->with('success', 'Escala criada.');
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule, UpdateSchedule $action): RedirectResponse
    {
        $action->update($schedule, $request->validated());

        return back()->with('success', 'Escala atualizada.');
    }

    public function destroy(Schedule $schedule, DeleteSchedule $action): RedirectResponse
    {
        $action->delete($schedule);

        return back()->with('success', 'Escala removida.');
    }
}
