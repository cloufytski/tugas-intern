<?php

namespace Modules\MaterialProc\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\MaterialProc\Models\Procurement;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class ProcurementDataTable extends DataTable
{
    protected $table = 'procurement_ts';

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('plant_description', function ($row) {
                return $row->plant_description;
            })
            ->filterColumn('plant_description', function ($query, $keyword) {
                $query->where('p.description', 'ILIKE', "%{$keyword}%");
            })
            ->addColumn('supplier', function ($row) {
                return $row->supplier;
            })
            ->filterColumn('supplier', function ($query, $keyword) {
                $query->where('s.supplier', 'ILIKE', "%{$keyword}%");
            })
            ->addColumn('material_description', function ($row) {
                return $row->material_description;
            })
            ->filterColumn('material_description', function ($query, $keyword) {
                $query->where('m.material_description', 'ILIKE', "%{$keyword}%");
            })
            ->addColumn('action', 'procurement.action')
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    public function query(Procurement $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->select("$this->table.*", "p.description as plant_description", "m.material_description", "s.supplier")
            ->join("material_master as m", "$this->table.id_material", "=", "m.id")
            ->join("supplier_ms as s", "$this->table.id_supplier", "=", "s.id")
            ->join("plant_master as p", "$this->table.id_plant", "=", "p.id")
            ->with(['history' => function ($q) {
                $q->orderByDesc('created_at');
            }])
            ->orderBy("$this->table.eta", "ASC");

        if ($this->request->has('start_date') && $this->request->has('end_date')) {
            $query->whereBetween("$this->table.eta", [$this->request->input('start_date'), $this->request->input('end_date')]);
        }

        return $query;
    }

    protected function filename(): string
    {
        return 'Procurement_' . date('YmdHis');
    }
}
