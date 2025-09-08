<?php

namespace App\DataTables;

use App\Models\Master\Material\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class MaterialDataTable extends DataTable
{
    protected $table = 'material_master';

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('class', function ($row) {
                return $row->class;
            })
            ->filterColumn('class', function ($query, $keyword) {
                $query->where('material_class_master.class', 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('class', function ($query, $order) {
                $query->orderBy('material_class_master.class', $order);
            })
            ->addColumn('product_category', function ($row) {
                return $row->product_category;
            })
            ->filterColumn('product_category', function ($query, $keyword) {
                $query->where('material_category_master.product_category', 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('product_category', function ($query, $order) {
                $query->orderBy('material_category_master.product_category', $order);
            })
            ->addColumn('product_group', function ($row) {
                return $row->product_group;
            })
            ->filterColumn('product_group', function ($query, $keyword) {
                $query->where('material_group_master.product_group', 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('product_group', function ($query, $order) {
                $query->orderBy('material_group_master.product_group', $order);
            })
            ->addColumn('product_group_simple', function ($row) {
                return $row->product_group_simple;
            })
            ->filterColumn('product_group_simple', function ($query, $keyword) {
                $query->where('material_group_simple_master.product_group_simple', 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('product_group_simple', function ($query, $order) {
                $query->orderBy('material_group__simple_master.product_group_simple', $order);
            })
            ->addColumn('packaging', function ($row) {
                return $row->packaging;
            })
            ->filterColumn('packaging', function ($query, $keyword) {
                $query->where('material_packaging_master.packaging', 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('packaging', function ($query, $order) {
                $query->orderBy('material_packaging_master.packaging', $order);
            })
            ->addColumn('action', '') // Action defined in blade.php
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    public function query(Material $model): QueryBuilder
    {
        return $model->newQuery()->withTrashed()
            ->select(
                "{$this->table}.*",
                'material_class_master.class',
                'material_category_master.product_category',
                'material_group_master.product_group',
                'material_group_simple_master.product_group_simple',
                'material_packaging_master.packaging'
            )
            ->join('material_class_master', "{$this->table}.id_class", '=', 'material_class_master.id')
            ->join('material_category_master', "{$this->table}.id_category", '=', 'material_category_master.id')
            ->join('material_group_master', "{$this->table}.id_group", '=', 'material_group_master.id')
            ->join('material_group_simple_master', "{$this->table}.id_group_simple", '=', 'material_group_simple_master.id')
            ->join('material_packaging_master', "{$this->table}.id_packaging", '=', 'material_packaging_master.id');
    }

    protected function filename(): string
    {
        return 'Material_' . date('YmdHis');
    }
}
