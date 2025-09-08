<?php

namespace Modules\MaterialProc\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Modules\MaterialProc\Services\ProcurementService;

class ProcurementImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{

    public function __construct(
        protected string $sheetName,
        protected ProcurementService $procurementService,
    ) {}

    public function sheets(): array
    {
        return [
            $this->sheetName => $this,
        ];
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $data = [
                'contract_no' => $row['contract_no'],
                'po_date' => Carbon::instance(Date::excelToDateTimeObject($row['po_date'])),
                'id_supplier' => $row['id_supplier'],
                'id_material' => $row['id_material'],
                'id_plant' => $row['id_plant'],
                'qty' => $row['qty'],
                'qty_actual' => $row['qty_actual'],
                'qty_plan' => $row['qty_plan'],
                'eta' => Carbon::instance(Date::excelToDateTimeObject($row['eta'])),
                'eta_actual' => Carbon::instance(Date::excelToDateTimeObject($row['eta_actual'])),
                'eta_plan' => Carbon::instance(Date::excelToDateTimeObject($row['eta_plan'])),
                'vessel_name' => $row['vessel_name'],
                'loading_port' => $row['loading_port'],
                'ffa' => $row['ffa'],
                'price' => $row['price'],
                'remarks' => $row['remarks'],
                'is_rspo' => $row['is_rspo'],
            ];
            $this->procurementService->create($data);
        }
    }
}
