<?php

namespace Modules\MaterialProc\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\MaterialProc\Repositories\Implementations\SupplierRepository;
use Modules\MaterialProc\Repositories\Interfaces\SupplierRepositoryInterface;
use Modules\MaterialProc\Transformers\SupplierResource;
use Modules\MaterialProc\Transformers\SupplierSearchResource;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierRepositoryInterface $repository,
    ) {}

    public function index(Request $request)
    {
        if ($request->has('supplier')) {
            $data = $this->repository->findBySupplier($request['supplier']);
        } else if ($request->ajax() && $request->has('is_datatable')) {
            $data = $this->repository->query();
            return DataTables::eloquent($data)->setRowId('id')->make(true);
        } else {
            $data = $this->repository->all();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                SupplierResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => SupplierResource::collection($data),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('master-supplier-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $validate = $request->validate([
            'supplier' => 'required|string',
            'certificate_no' => 'nullable|string|max:50',

        ]);
        $data = $this->repository->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Supplier is successfully added.',
            'data' => SupplierResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        abort_unless(Auth::user()->hasPermission('master-supplier-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->findById($id);
        return response()->json([
            'success' => true,
            'data' => SupplierResource::make($data),
        ]);
    }

    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('master-supplier-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->repository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'Supplier ' . $data->supplier . ' is successfully restored.',
                'data' => SupplierResource::make($data),
            ]);
        } else {
            $validate = $request->validate([
                'supplier' => 'required|string',
                'certificate_no' => 'nullable|string|max:50',
            ]);
            $data = $this->repository->update($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Supplier ' . $data->supplier . ' is successfully updated.',
                'data' => SupplierResource::make($data),
            ]);
        }
    }

    public function destroy($id)
    {
        abort_unless(Auth::user()->hasPermission('master-supplier-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Supplier ' . $data->supplier . ' is successfully deleted.',
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('master-supplier-read')) {
            return redirect()->route('unauthorized');
        }
        return view('materialproc::contents.supplier.supplier-ms');
    }
}
