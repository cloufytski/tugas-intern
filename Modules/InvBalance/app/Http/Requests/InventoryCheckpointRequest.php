<?php

namespace Modules\InvBalance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryCheckpointRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'checkpoints' => 'required|array|min:1',
            'checkpoints.*.date' => 'nullable|date',
            'checkpoints.*.product_group' => 'required|string',
            'checkpoints.*.id_group' => 'nullable|integer',
            'checkpoints.*.beginning_balance' => 'nullable|numeric',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
