<?php

namespace App\Http\Controllers\Master\Material;

use App\DataTables\MaterialDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Http\Resources\Master\Material\MaterialResource;
use App\Repositories\Interfaces\MaterialRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{

    public function __construct(
        protected MaterialRepositoryInterface $materialRepository,
    ) {}

    public function index(Request $request, MaterialDataTable $materialDataTable)
    {
        if ($request->has('material_description')) {
            $data = $this->materialRepository->findByMaterialDescription($request['material_description']);
        } else if ($request->ajax() && $request->has('is_datatable')) {
            return $materialDataTable->ajax();
        } else {
            $data = $this->materialRepository->all();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                MaterialResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => MaterialResource::collection($data),
        ]);
    }

    public function store(MaterialRequest $request)
    {
        abort_unless(Auth::user()->hasPermission('master-material-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->materialRepository->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Material ' . $data->material_description . ' is successfully added.',
            'data' => MaterialResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->materialRepository->findById($id);
        return response()->json([
            'success' => true,
            'data' => MaterialResource::make($data),
        ]);
    }

    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->materialRepository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'Material ' . $data->material_description . ' is successfully restored.',
                'data' => MaterialResource::make($data),
            ]);
        }
        $request = app(MaterialRequest::class);
        $data = $this->materialRepository->update($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Material ' . $data->material_description . ' is successfully updated.',
            'data' => MaterialResource::make($data),
        ]);
    }

    public function destroy(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-material-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->materialRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Material ' . $data->material_description . ' is successfully deleted.',
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('master-material-read')) {
            return redirect()->route('unauthorized');
        }
        return view('contents.masters.material.material-master');
    }
}
