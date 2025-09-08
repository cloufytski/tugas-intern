<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\Master\PlantResource;
use App\Repositories\Interfaces\PlantRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PlantController extends Controller
{
    public function __construct(
        protected PlantRepositoryInterface $plantRepository,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('is_datatable')) {
            $data = $this->plantRepository->query();
            return DataTables::eloquent($data)->setRowId('id')->make(true);
        } else {
            $data = $this->plantRepository->all();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                PlantResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => PlantResource::collection($data),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $validate = $request->validate([
            'plant' => 'required|string',
            'description' => 'required|string',
        ]);
        $data = $this->plantRepository->create($validate);
        return response()->json([
            'success' => true,
            'message' => 'Plant ' . $data->description . ' is successfully added.',
            'data' => PlantResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->plantRepository->findById($id);
        return response()->json([
            'success' => true,
            'data' => PlantResource::make($data),
        ]);
    }

    public function update(Request $request, string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->plantRepository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'Plant ' . $data->description . ' is successfully restored.',
                'data' => PlantResource::make($data),
            ]);
        } else {
            $validate = $request->validate([
                'plant' => 'required|string',
                'description' => 'required|string',
            ]);
            $data = $this->plantRepository->update($id, $validate);
            return response()->json([
                'success' => true,
                'message' => 'Plant ' . $data->description . ' is successfully updated.',
                'data' => PlantResource::make($data),
            ]);
        }
    }

    public function destroy(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->plantRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Plant ' . $data->description . ' is successfully deleted.',
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('master-plant-read')) {
            return redirect()->route('unauthorized');
        }
        return view('contents.masters.plant.plant-master');
    }
}
