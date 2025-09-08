<?php

namespace App\Http\Controllers\Master\Material;

use App\Http\Controllers\Controller;
use App\Http\Resources\Master\Material\MaterialGroupResource;
use App\Repositories\Interfaces\MaterialGroupRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MaterialGroupController extends Controller
{
    public function __construct(
        protected MaterialGroupRepositoryInterface $repository,
    ) {}

    public function index(Request $request)
    {
        if ($request->has('product_group')) {
            $data = $this->repository->searchByProductGroup($request->get('product_group') ?? '');
        } else if ($request->has('categories')) {
            $data = $this->repository->searchByCategories($request->get('categories') ?? '');
        } else if ($request->ajax() && $request->has('is_datatable')) {
            $data = $this->repository->query();
            return DataTables::eloquent($data)->setRowId('id')->make(true);
        } else {
            $data = $this->repository->all();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                MaterialGroupResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => MaterialGroupResource::collection($data),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('master-material-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $validate = $request->validate([
            'id_category' => 'required|integer',
            'product_group' => 'required|string',
            'min_threshold' => 'nullable|numeric',
            'max_threshold' => 'nullable|numeric',
        ]);
        $data = $this->repository->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Material Group ' . $data->product_group . ' is successfully added.',
            'data' => MaterialGroupResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->findById($id);
        return response()->json([
            'success' => true,
            'data' => MaterialGroupResource::make($data),
        ]);
    }

    public function update(Request $request, string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->repository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'Material Group ' . $data->product_group . ' is successfully restored.',
                'data' => MaterialGroupResource::make($data),
            ]);
        } else {
            $validate = $request->validate([
                'id_category' => 'required|integer',
                'product_group' => 'required|string',
                'min_threshold' => 'nullable|numeric',
                'max_threshold' => 'nullable|numeric',
            ]);
            $data = $this->repository->update($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Material Group ' . $data->product_group . ' is successfully updated.',
                'data' => MaterialGroupResource::make($data),
            ]);
        }
    }

    public function destroy(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->repository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Material Group ' . $data->product_group . ' is successfully deleted.',
        ]);
    }
}
