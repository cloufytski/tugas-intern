<?php

namespace App\Repositories\Implementations\Master\Material;

use App\Models\Master\Material\MaterialGroup;
use App\Repositories\Interfaces\MaterialGroupRepositoryInterface;
use App\Traits\LogTransactionTrait;

class MaterialGroupRepository implements MaterialGroupRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'MATERIAL_GROUP';

    public function searchByIds(array $groupIds)
    {
        return MaterialGroup::whereIn('id', $groupIds)->get();
    }

    public function searchByProductGroups(array $productGroups)
    {
        return MaterialGroup::whereIn('product_group', $productGroups)->get();
    }

    public function firstByProductGroup(string $productGroup)
    {
        return MaterialGroup::firstWhere('product_group', $productGroup);
    }

    public function searchByCategories(array $categoryIds)
    {
        return MaterialGroup::with('category')
            ->whereIn('id_category', $categoryIds)
            ->get();
    }

    public function searchByProductGroup(string $productGroup, $limit = 30)
    {
        return MaterialGroup::where('product_group', 'ILIKE', "%{$productGroup}%")
            ->limit($limit)
            ->get();
    }

    public function query()
    {
        return MaterialGroup::query()->with('category')
            ->withTrashed();
    }

    public function all()
    {
        return MaterialGroup::query()->with('category')
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return MaterialGroup::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = MaterialGroup::create($data);
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
        $model = MaterialGroup::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
