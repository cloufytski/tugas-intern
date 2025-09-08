<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Master\Material\MaterialUom;
use App\Repositories\Interfaces\MaterialUomRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialUomRepository implements MaterialUomRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL_UOM';

    public function firstByUom(string $uom)
    {
        return MaterialUom::firstWhere('uom', $uom) ??
            MaterialUom::firstWhere('uom', 'ILIKE', "{$uom}%");
    }

    public function searchByUom(string $uom)
    {
        return MaterialUom::where('uom', 'ILIKE', "%{$uom}%")
            ->get();
    }

    public function query()
    {
        return MaterialUom::query()->withTrashed();
    }

    public function all()
    {
        return MaterialUom::query()
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return MaterialUom::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = MaterialUom::create($data);
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
        $model = MaterialUom::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
