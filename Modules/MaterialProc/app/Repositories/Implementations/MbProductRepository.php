<?php

namespace Modules\MaterialProc\Repositories\Implementations;

use Modules\MaterialProc\Models\Procurement;
use Modules\MaterialProc\Repositories\Interfaces\MbProductRepositoryInterface;

class MbProductRepository implements MbProductRepositoryInterface
{
    public function getInputProducts(string $startDate, string $endDate, bool $isRspo = true)
    {
        return Procurement::with([
            'supplier:id,supplier,certificate_no',
            'material:id,material_description,conversion,id_group',
            'material.productGroup:id,product_group'
        ])
            ->whereBetween('procurement_ts.eta', [$startDate, $endDate])
            ->where('is_rspo', $isRspo)
            ->select([
                'procurement_ts.id as procurement_id',
                'procurement_ts.id_supplier',
                'procurement_ts.id_material',
                'procurement_ts.qty',
                'procurement_ts.eta',
            ])
            ->get();
    }
}
