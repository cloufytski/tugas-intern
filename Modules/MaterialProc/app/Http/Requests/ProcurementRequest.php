<?php

namespace Modules\MaterialProc\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcurementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'contract_no' => 'required|string',
            'id_supplier' => 'nullable|integer|required_without:supplier',
            'supplier' => 'nullable|string|required_without:id_supplier',
            'id_material' => 'nullable|integer|required_without:material_description',
            'material_description' => 'nullable|string|required_without:id_material',
            'qty_plan' => 'required|numeric',
            'eta_plan' => 'required|date',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'id_supplier.required_without' => 'Supplier field is required',
            'id_material.required_without' => 'Material description field is required',
        ];
    }
}
