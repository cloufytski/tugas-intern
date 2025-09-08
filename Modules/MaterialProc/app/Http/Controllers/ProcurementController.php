<?php

namespace Modules\MaterialProc\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\MaterialProc\DataTables\ProcurementDataTable;
use Modules\MaterialProc\Http\Requests\ProcurementRequest;
use Modules\MaterialProc\Services\ProcurementService;
use Modules\MaterialProc\Transformers\ProcurementResource;

class ProcurementController extends Controller
{
    public function __construct(
        protected ProcurementService $procurementService,
    ) {}

    public function index(Request $request, ProcurementDataTable $dataTable)
    {
        if ($request->ajax() && $request->has('is_datatable')) {
            return $dataTable->ajax();
        } else {
            $data = $this->procurementService->query();
            return (new AnonymousResourceCollection(
                $data->paginate(config('constants.paginate')),
                ProcurementResource::class
            ))->response();
        }
        return response()->json([
            'success' => true,
            'data' => ProcurementResource::collection($data),
        ]);
    }

    public function store(ProcurementRequest $request)
    {
        abort_unless(Auth::user()->hasPermission('procurement-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $request->validated();
        $data = $this->procurementService->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Procurement ' . $data->id . ' is successfully added.',
            'data' => ProcurementResource::make($data),
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        abort_unless(Auth::user()->hasPermission('procurement-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->procurementService->findById($id);
        return response()->json([
            'success' => true,
            'data' => ProcurementResource::make($data),
        ]);
    }

    public function update(ProcurementRequest $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('procurement-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $request->validated();
        $data = $this->procurementService->update($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Procurement ' . $id . ' is successfully updated.',
            'data' => ProcurementResource::make($data),
        ]);
    }

    public function updateQtyOrEta(Request $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('procurement-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $validate = $request->validate([
            'qty_actual' => 'numeric',
            'qty_plan' => 'numeric',
            'eta_actual' => 'date',
            'eta_plan' => 'date',
        ]);
        $data = $this->procurementService->updateQtyOrEta($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Procurement ' . $id . ' is successfully updated.',
            'data' => ProcurementResource::make($data),
        ]);
    }

    public function destroy($id)
    {
        abort_unless(Auth::user()->hasPermission('procurement-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->procurementService->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Procurement ' . $id . ' is successfully deleted.',
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('procurement-read')) {
            return redirect()->route('unauthorized');
        }
        return view('materialproc::contents.procurement.procurement-ts');
    }
}
