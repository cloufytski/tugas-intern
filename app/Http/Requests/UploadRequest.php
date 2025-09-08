<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // size in kB, maximum 2 MB = 2000 kB
            'file' => 'required|file|max:2000|mimes:xlsx,xls',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'No file uploaded.',
        ];
    }
}
