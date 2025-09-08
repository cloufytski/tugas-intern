<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_restore' => 'boolean',
            'material_description' => 'required|string',
            'id_class' => 'required|integer',
            'id_category' => 'required|integer',
            'id_metric' => 'required|integer',
            'id_group_simple' => 'required|integer',
            'id_group' => 'required|integer',
            'id_packaging' => 'required|integer',
            'id_pp_class' => 'required|integer',
            'id_pv_class' => 'required|integer',
            'id_uom' => 'required|integer',
            'rate' => 'required|numeric',
            'conversion' => 'required|numeric',
        ];
    }
}
