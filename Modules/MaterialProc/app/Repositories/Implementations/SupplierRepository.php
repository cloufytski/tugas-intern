<?php

namespace Modules\MaterialProc\Repositories\Implementations;

use App\Traits\LogTransactionTrait;
use Modules\MaterialProc\Models\Supplier;
use Modules\MaterialProc\Repositories\Interfaces\SupplierRepositoryInterface;

class SupplierRepository implements SupplierRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MaterialProc';
    protected $modelName = 'SUPPLIER';

    public function findBySupplier(string $supplier)
    {
        return Supplier::where('supplier', 'ILIKE', "%{$supplier}%") // NOTE: ILIKE only available on Postgresql case-insensitive
            ->orderBy('id', 'ASC')
            ->select('id', 'supplier')
            ->get();
    }

    public function firstBySupplier(string $supplier)
    {
        return Supplier::firstWhere('supplier', $supplier);
    }

    public function query()
    {
        // query to support DataTables, and withTrashed to include SoftDeletes
        return Supplier::query()->withTrashed();
    }

    public function all()
    {
        return Supplier::query()
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return Supplier::findOrFail($id);
    }

    public function create(array $data)
    {
        $model = Supplier::create($data);
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
        $model = Supplier::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
