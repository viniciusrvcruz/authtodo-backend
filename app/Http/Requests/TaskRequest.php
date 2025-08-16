<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                Rule::when($this->isMethod('post'), ['required'], ['sometimes']),
                'string',
                'max:255'
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_completed' => ['boolean'],
        ];
    }
}
