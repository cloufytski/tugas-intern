<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Master\Material\MaterialGroupSimple;
use App\Repositories\Interfaces\MasterRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialGroupSimpleRepository implements MasterRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL_GROUP_SIMPLE';

    public function searchByProductGroupSimple(string $productGroupSimple, $limit = 30)
    {
        return MaterialGroupSimple::where('product_group_simple', 'ILIKE', "%{$productGroupSimple}%")
            ->limit($limit)
            ->get();
    }

    public function query()
    {
        return MaterialGroupSimple::query()->with('category')
            ->withTrashed();
    }

    public function all()
    {
        return MaterialGroupSimple::query()->with('category')
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return MaterialGroupSimple::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = MaterialGroupSimple::create($data);
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
        $model = MaterialGroupSimple::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
