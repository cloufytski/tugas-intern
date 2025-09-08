<?php

namespace Modules\MaterialProc\Services;

use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Repositories\Interfaces\PlantRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\MaterialProc\Repositories\Implementations\ProcurementHistoryRepository;
use Modules\MaterialProc\Repositories\Interfaces\ProcurementRepositoryInterface;
use Modules\MaterialProc\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Helpers\Utils;
use Illuminate\Support\Facades\Auth;

class ProcurementService
{
    public function __construct(
        protected ProcurementRepositoryInterface $procurementRepository,
        protected ProcurementHistoryRepository $procurementHistoryRepository,
        protected SupplierRepositoryInterface $supplierRepository,
        protected MaterialRepositoryInterface $materialRepository,
        protected PlantRepositoryInterface $plantRepository,
    ) {}

    public function getTotalPerDateGroup(?string $startDate, ?string $endDate, array $params)
    {
        $data = $this->procurementRepository->getTotalByDateGroup($startDate, $endDate, $params);
        foreach ($data as $item) {
            $item->date = Utils::constructPeriodToDate($item->period, $params['date_group'], $startDate);
        }
        return [
            'data' => $data,
        ];
    }

    public function query()
    {
        return $this->procurementRepository->query();
    }

    public function findById(int $id)
    {
        return $this->procurementRepository->findById($id);
    }

    public function create(array $data)
    {
        $this->constructForeignId($data);
        $this->constructQtyAndEta($data);

        if (!Auth::user()->hasPermission('procurement-price-create')) {
            unset($data['price']);
        }

        return $this->procurementRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $procurement = $this->findById($id);
        $this->constructForeignId($data);
        $this->constructQtyAndEta($data, $procurement);
        $this->constructHistory($data, $procurement);

        if (!Auth::user()->hasPermission('procurement-price-update')) {
            unset($data['price']);
        }

        return $this->procurementRepository->update($id, $data);
    }

    public function updateQtyOrEta(int $id, array $data)
    {
        $procurement = $this->findById($id);
        // skip construct foreign id, because not sending Supplier and Material; only Qty or ETA
        $data['id_plant'] = $procurement->id_plant;
        $data['id_supplier'] = $procurement->id_supplier;
        $data['id_material'] = $procurement->id_material;

        $this->constructQtyAndEta($data, $procurement);
        $this->constructHistory($data, $procurement);

        return $this->procurementRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->procurementRepository->delete($id);
    }

    private function constructQtyAndEta(array &$data, $procurement = null)
    {
        $data['qty'] = $data['qty_actual'] ?? optional($procurement)->qty_actual ?? $data['qty_plan'] ?? optional($procurement)->qty_plan ?? null;
        $data['eta'] = $data['eta_actual'] ?? optional($procurement)->eta_actual ?? $data['eta_plan'] ?? optional($procurement)->eta_plan ?? null;
    }

    private function constructForeignId(array &$data)
    {
        if (!isset($data['id_material'])) {
            $data['id_material'] = $this->getMaterialId($data['material_description']);
        }
        if (!isset($data['id_supplier'])) {
            $data['id_supplier'] = $this->getSupplierId($data['supplier']);
        }
        if (!isset($data['id_plant'])) {
            $data['id_plant'] = $this->getPlantId($data['plant'], $data['description'] ?? null);
        }
    }

    private function getSupplierId(string $supplier)
    {
        $supplier = $this->supplierRepository->firstBySupplier($supplier);
        if (!$supplier) {
            throw new ModelNotFoundException("Supplier not found.");
        }
        return $supplier->id;
    }

    private function getMaterialId(string $materialDescription)
    {
        $material = $this->materialRepository->firstByMaterialDescription($materialDescription);
        if (!$material) {
            throw new ModelNotFoundException("Material not found.");
        }
        return $material->id;
    }

    private function getPlantId($plant, $description)
    {
        $plant = $this->plantRepository->firstByPlant($plant);
        if (!$plant && $description !== null) {
            $plant = $this->plantRepository->firstByDescription($description);
        }
        if (!$plant) {
            throw new ModelNotFoundException("Plant not found. Given: plant='{$plant}', description='{$description}'");
        }

        return $plant->id;
    }

    private function constructHistory(array &$data, &$procurement)
    {
        foreach (['qty_actual', 'qty_plan', 'eta_actual', 'eta_plan'] as $field) {
            if (isset($data[$field]) && $procurement->$field !== null && (string)$procurement->$field !== (string)$data[$field]) {
                $this->procurementHistoryRepository->create([
                    'id_procurement' => $procurement->id,
                    'field_name' => $field,
                    'old_value' => $procurement->$field,
                    'new_value' => $data[$field],
                    'history_remarks' => $data['history_remarks'] ?? null,
                ]);
            }
        }
    }
}
