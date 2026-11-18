<?php

namespace App\Http\Requests\Schedules;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Team $team */
        $team = $this->route('team');

        return $this->user()->can('manageSchedules', $team);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Team $team */
        $team = $this->route('team');

        return [
            'service_id' => [
                'required',
                Rule::exists('services', 'id')->where('team_id', $team->id),
            ],
            'person_id' => [
                'required',
                Rule::exists('people', 'id')->where('team_id', $team->id),
            ],
            'role' => ['required', 'string', 'max:50'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'declined'])],
        ];
    }
}
