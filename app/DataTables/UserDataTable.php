<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('roles', function ($query) {
                return $query->roles->pluck('display_name')->implode(', ');
            })
            ->addColumn('action', '') // Action defined in blade.php
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->withTrashed()->with('roles');
    }

    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
