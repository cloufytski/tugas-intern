<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Http\Resources\Log\LogTransactionResource;
use App\Repositories\Implementations\Log\LogTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LogTransactionController extends Controller
{
    public function __construct(
        protected LogTransactionRepository $repository,
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
                LogTransactionResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => LogTransactionResource::collection($data),
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('developer-update')) {
            return redirect()->route('unauthorized');
        }
        return view('contents.logs.log-transaction');
    }
}
