<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Http\Resources\Log\LogModuleResource;
use App\Repositories\Implementations\Log\LogModuleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Yajra\DataTables\Facades\DataTables;

class LogModuleController extends Controller
{
    public function __construct(
        protected LogModuleRepository $repository,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('is_datatable')) {
            $data = $this->repository->query();
            return DataTables::eloquent($data)->setRowId('id')->make(true);
        } else {
            $data = $this->repository->query();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                LogModuleResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => LogModuleResource::collection($data),
        ]);
    }
}
