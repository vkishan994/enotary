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
            'price' => 'required|numeric|min:0',
            'notary_service_types' => 'array',
            'notary_service_types.*' => 'exists:notary_service_types,id',
            'upload_documents' => 'required|array|min:1',
            'upload_documents.*' => 'exists:upload_documents,id',
        ];
    }

    public function messages(): array
    {
        return [
            'upload_documents.required' => 'This is a required field',
        ];
    }
}
