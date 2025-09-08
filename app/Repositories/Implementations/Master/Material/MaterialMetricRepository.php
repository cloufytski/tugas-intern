<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialMetric;
use App\Repositories\Interfaces\MasterRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialMetricRepository implements MasterRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL_METRIC';

    public function searchByProductMetric(string $productMetric, $limit = 30)
    {
        return MaterialMetric::where('product_metric', 'ILIKE', "%{$productMetric}%")
            ->limit($limit)
            ->get();
    }

    public function query()
    {
        return MaterialMetric::query()->with('category')
            ->withTrashed();
    }

    public function all()
    {
        return MaterialMetric::query()->with('category')
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return MaterialMetric::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = MaterialMetric::create($data);
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
        $model = MaterialMetric::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
