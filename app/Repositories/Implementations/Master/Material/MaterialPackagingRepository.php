<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Master\Material\MaterialPackaging;
use App\Repositories\Interfaces\MasterRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialPackagingRepository implements MasterRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL_PACKAGING';

    public function searchByPackaging(string $packaging, $limit = 30)
    {
        return MaterialPackaging::where('packaging', 'ILIKE', "%{$packaging}%")
            ->limit($limit)
            ->get();
    }

    public function query()
    {
        return MaterialPackaging::query()->withTrashed();
    }

    public function all()
    {
        return MaterialPackaging::query()
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return MaterialPackaging::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = MaterialPackaging::create($data);
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
        $model = MaterialPackaging::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
