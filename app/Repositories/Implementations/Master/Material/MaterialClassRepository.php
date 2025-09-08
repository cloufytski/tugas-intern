<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialClass;
use App\Repositories\Interfaces\MasterRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialClassRepository implements MasterRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL_CLASS';

    public function searchByClass(string $class)
    {
        return MaterialClass::where('class', 'ILIKE', "%{$class}%")
            ->get();
    }

    public function query()
    {
        return MaterialClass::query()->withTrashed();
    }

    public function all()
    {
        return MaterialClass::query()
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return MaterialClass::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = MaterialClass::create($data);
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
        $model = MaterialClass::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
