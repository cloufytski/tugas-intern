<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Master\Material\Material;
use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialRepository implements MaterialRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL';

    private $materialJoin = [
        'productCategory:id,product_category',
        'productGroupSimple:id,product_group_simple',
        'productGroup:id,product_group',
    ];

    public function findByMaterialDescription(string $materialDescription)
    {
        return Material::where('material_description', 'ILIKE', "{$materialDescription}%")
            ->select('id', 'material_description')
            ->get();
    }

    public function firstByMaterialDescription(string $materialDescription)
    {
        return Material::firstWhere('material_description', $materialDescription);
    }

    public function query()
    {
        return Material::query()->with($this->materialJoin)
            ->withTrashed();
    }

    public function all()
    {
        return Material::query()->with($this->materialJoin)
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        $allJoin = [
            'class:id,class',
            'productCategory:id,product_category',
            'productMetric:id,product_metric',
            'productGroupSimple:id,product_group_simple',
            'productGroup:id,product_group',
            'uom:id,uom',
            'packaging:id,packaging',
            'ppClass:id,packaging_class',
            'pvClass:id,packaging_class',
        ];
        return Material::findOrFail($id)->load($allJoin);
    }

    public function create(array $data)
    {
        $model = Material::create($data);
        $this->log(logType: 'CREATE', model: $model, data: $data);
        return $model;
    }

    public function update(int $id, array $data)
    {
        $model = $this->findById($id);
        $this->log(logType: 'UPDATE', model: $model, data: $data);
        $model->update($data);
        return $model;
    }

    public function delete(int $id)
    {
        $model = $this->findById($id);
        $model->delete();
        $this->log(logType: 'DELETE', model: $model, data: $id);
        return $model;
    }

    public function restore(int $id)
    {
        $model = Material::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
