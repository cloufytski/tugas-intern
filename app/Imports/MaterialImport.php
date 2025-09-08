<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class MaterialImport implements ToCollection, WithHeadingRow, WithMultipleSheets, WithChunkReading, WithProgressBar
{
    use Importable;
    private $sheetName;
    private $table = 'material_master';

    public function __construct($sheetName)
    {
        $this->sheetName = $sheetName;
    }

    public function sheets(): array
    {
        return [
            $this->sheetName => $this, // only process defined sheetName
        ];
    }

    public function collection(Collection $collection)
    {
        $data = [];

        foreach ($collection as $row) {
            $data[] = [
                'id' => $row['id'], // generated from excel file
                'material' => $row['material'],
                'material_description' => trim($row['material_description']),
                'id_class' => $row['id_class'],
                'id_category' => $row['id_category'],
                'id_metric' => $row['id_metric'],
                'id_group_simple' => $row['id_group_simple'],
                'id_group' => $row['id_group'],
                'id_packaging' => $row['id_packaging'],
                'id_uom' => $row['id_uom'],
                'rate' => $row['rate'],
                'conversion' => $row['conversion'],
                'space' => $this->convertBlankToNull($row['space']),
                'id_pp_class' => $row['id_pp_class'],
                'id_pv_class' => $row['id_pv_class'],
                'kind_of_pack' => $this->convertBlankToNull($row['kind_of_pack']),
                'base_price' => $row['base_price'],
                'pack_cost' => $this->convertBlankToNull($row['pack_cost']),
                'devider' => $row['devider'],
                'bus_line' => $this->convertBlankToNull($row['bus_line']),
                'rm' => $this->convertBlankToNull($row['rm']),
                'conversion_to_rm' => $this->convertBlankToNull($row['conversion_to_rm']),
                // 'base_product' => $this->convertBlankToNull($row['base_product']), // base product is recurse to Material Id
                'rumus_molecul' => $this->convertBlankToNull($row['rumus_molecul']),
                'auto_produce' => $this->convertBlankToNull($row['auto_produce']),
                'eudr' => $row['eudr'],
                'eudr_sale' => $row['eudr_sale'],
                'hs_code' => $this->convertBlankToNull($row['hs_code']),
            ];
        }

        DB::table($this->table)->insert($data);

        // Increment id sequence
        DB::statement("SELECT setval('{$this->table}_id_seq', (SELECT MAX(id) FROM {$this->table}))");
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function convertBlankToNull($value)
    {
        return !empty(trim($value)) ? trim($value) : null;
    }
}
