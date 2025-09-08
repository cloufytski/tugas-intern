<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\Master\SectionResource;
use App\Repositories\Interfaces\SectionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SectionController extends Controller
{

    public function __construct(
        protected SectionRepositoryInterface $sectionRepository
    ) {}

    public function index(Request $request)
    {
        if ($request->has('id_plant') && is_array($request['id_plant'])) {
            $data = $this->sectionRepository->findByPlantArray($request['id_plant']);
        } else if ($request->has('id_plant')) {
            $data = $this->sectionRepository->findByPlant($request['id_plant']);
        } else if ($request->ajax() && $request->has('is_datatable')) {
            $data = $this->sectionRepository->query();
            return DataTables::eloquent($data)->setRowId('id')->make(true);
        } else {
            $data = $this->sectionRepository->all();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                SectionResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => SectionResource::collection($data),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $validate = $request->validate([
            'id_plant' => 'required|integer',
            'section' => 'required|string',
        ]);
        $data = $this->sectionRepository->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Section ' . $data->section . ' is successfully added.',
            'data' => SectionResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->sectionRepository->findById($id);
        return response()->json([
            'success' => true,
            'data' => SectionResource::make($data),
        ]);
    }

    public function update(Request $request, string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->sectionRepository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'Section ' . $data->section . ' is successfully restored.',
                'data' => SectionResource::make($data),
            ]);
        } else {
            $validate = $request->validate([
                'id_plant' => 'required|integer',
                'section' => 'required|string',
            ]);
            $data = $this->sectionRepository->update($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Section ' . $data->section . ' is successfully updated.',
                'data' => SectionResource::make($data),
            ]);
        }
    }

    public function destroy(string $id)
    {
        abort_unless(Auth::user()->hasPermission('master-plant-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->sectionRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Section ' . $data->section . ' is successfully deleted.',
        ]);
    }
}
