<?php

namespace Modules\InvBalance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryTotalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'plants' => 'nullable|array',
            'order_statuses' => 'nullable|array',
            'categories' => 'required|array|min:1',
            'groups' => 'nullable|array', // optional because query without id_group grouping
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'Please provide at least 1 Product Category in Filter.',
        ];
    }
}
