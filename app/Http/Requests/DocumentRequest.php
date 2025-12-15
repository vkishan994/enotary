<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'notary_service_types' => 'array',
            'notary_service_types.*' => 'exists:notary_service_types,id',
        ];
    }
}

