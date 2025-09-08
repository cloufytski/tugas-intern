<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialPackagingClass;
use App\Repositories\Interfaces\MasterRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialPackagingClassRepository implements MasterRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL_PACKAGING_CLASS';

    public function searchByPackagingClass(string $packagingClass, $limit = 30)
    {
        return MaterialPackagingClass::where('packaging_class', 'ILIKE', "%{$packagingClass}%")
            ->limit($limit)
            ->get();
    }

    public function query()
    {
        return MaterialPackagingClass::query()->withTrashed();
    }

    public function all()
    {
        return MaterialPackagingClass::query()
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return MaterialPackagingClass::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = MaterialPackagingClass::create($data);
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
        $model = MaterialPackagingClass::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
