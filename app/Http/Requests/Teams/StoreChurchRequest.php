<?php

// app/Http/Requests/Teams/StoreChurchRequest.php

namespace App\Http\Requests\Teams;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Qualquer usuário logado e verificado pode criar a própria igreja
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'denomination' => ['nullable', 'string', 'max:80'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da igreja.',
            'name.min' => 'O nome precisa ter pelo menos 3 caracteres.',
            'city.required' => 'Informe a cidade.',
        ];
    }
}
