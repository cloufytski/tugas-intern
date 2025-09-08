<?php

namespace App\Http\Controllers\Master\Material;

use App\Http\Controllers\Controller;
use App\Http\Resources\Master\Material\MaterialGroupSimpleResource;
use App\Repositories\Implementations\Master\Material\MaterialGroupSimpleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MaterialGroupSimpleController extends Controller
{
    public function __construct(
        protected MaterialGroupSimpleRepository $repository,
    ) {}

    public function index(Request $request)
    {
        if ($request->has('product_group_simple')) {
            $data = $this->repository->searchByProductGroupSimple($request->get('product_group_simple') ?? '');
        } else if ($request->ajax() && $request->has('is_datatable')) {
            $data = $this->repository->query();
            return DataTables::eloquent($data)->setRowId('id')->make(true);
        } else {
            $data = $this->repository->all();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                MaterialGroupSimpleResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => MaterialGroupSimpleResource::collection($data),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('master-material-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $validate = $request->validate([
            'id_category' => 'required|integer',
            'product_group_simple' => 'required|string',
        ]);
        $data = $this->repository->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Material Group Simple ' . $data->product_group_simple . ' is successfully added.',
            'data' => MaterialGroupSimpleResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->findById($id);
        return response()->json([
            'success' => true,
            'data' => MaterialGroupSimpleResource::make($data),
        ]);
    }

    public function update(Request $request, string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->repository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'Material Group Simple ' . $data->product_group_simple . ' is successfully restored.',
                'data' => MaterialGroupSimpleResource::make($data),
            ]);
        } else {
            $validate = $request->validate([
                'id_category' => 'required|integer',
                'product_group_simple' => 'required|string',
            ]);
            $data = $this->repository->update($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Material Group Simple ' . $data->product_group_simple . ' is successfully updated.',
                'data' => MaterialGroupSimpleResource::make($data),
            ]);
        }
    }

    public function destroy(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Material Group Simple ' . $data->product_group_simple . ' is successfully deleted.',
        ]);
    }
}
