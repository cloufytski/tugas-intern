<?php

namespace App\Repositories\Implementations;

use App\Models\Master\Plant;
use App\Repositories\Interfaces\PlantRepositoryInterface;
use App\Traits\LogTransactionTrait;

class PlantRepository implements PlantRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'PLANT';

    public function firstByPlant(string $plant)
    {
        return Plant::firstWhere('plant', $plant);
    }

    public function firstByDescription(string $description)
    {
        return Plant::firstWhere('description', $description);
    }

    public function query()
    {
        // query to support DataTables, and withTrashed to include SoftDeletes
        return Plant::query()->withTrashed();
    }

    public function all()
    {
        return Plant::query()
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return Plant::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = Plant::create($data);
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
        $model = Plant::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
