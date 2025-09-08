<?php

namespace App\Http\Controllers\Master\Material;

use App\Http\Controllers\Controller;
use App\Http\Resources\Master\Material\MaterialCategoryResource;
use App\Repositories\Implementations\Master\Material\MaterialCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MaterialCategoryController extends Controller
{
    public function __construct(
        protected MaterialCategoryRepository $repository,
    ) {}

    public function index(Request $request)
    {
        if ($request->has('product_category')) {
            $data = $this->repository->searchByProductCategory($request->get('product_category') ?? '');
        } else if ($request->ajax() && $request->has('is_datatable')) {
            $data = $this->repository->query();
            return DataTables::eloquent($data)->setRowId('id')->make(true);
        } else {
            $data = $this->repository->all();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                MaterialCategoryResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => MaterialCategoryResource::collection($data),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('master-material-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $validate = $request->validate([
            'product_category' => 'required|string',
        ]);
        $data = $this->repository->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Material Category ' . $data->product_category . ' is successfully added.',
            'data' => MaterialCategoryResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->findById($id);
        return response()->json([
            'success' => true,
            'data' => MaterialCategoryResource::make($data),
        ]);
    }

    public function update(Request $request, string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->repository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'Material Category ' . $data->product_category . ' is successfully restored.',
                'data' => MaterialCategoryResource::make($data),
            ]);
        } else {
            $validate = $request->validate([
                'product_category' => 'required|string',
            ]);
            $data = $this->repository->update($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Material Category ' . $data->product_category . ' is successfully updated.',
                'data' => MaterialCategoryResource::make($data),
            ]);
        }
    }

    public function destroy(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Material Category ' . $data->product_category . ' is successfully deleted.',
        ]);
    }
}
