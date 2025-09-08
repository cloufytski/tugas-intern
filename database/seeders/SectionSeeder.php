<?php

namespace Database\Seeders;

use App\Models\Log\LogTransaction;
use App\Models\Master\Section;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [

            ['id_plant' => '1', 'section' => 'S201', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S202', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S203', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S204', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S205', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S207', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S208/271', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S210A', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S210B', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S210C', 'created_at' => $now],
            ['id_plant' => '1', 'section' => 'S210D', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S101/102', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S103', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S104', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S105', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S106', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S110', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S111/116', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S112', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S122A', 'created_at' => $now],
            ['id_plant' => '2', 'section' => 'S122B', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S123', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S123B', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S161', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S163', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S164', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S165', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S166', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S167', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S182', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S184', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S185A', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S185B', 'created_at' => $now],
            ['id_plant' => '3', 'section' => 'S302', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S701/702', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S703/704', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S705', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S706', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S707', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S708', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S712', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S714', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S722A', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S722B', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S722C', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S722D', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S722E', 'created_at' => $now],
            ['id_plant' => '4', 'section' => 'S722F', 'created_at' => $now],
            ['id_plant' => '6', 'section' => 'S303', 'created_at' => $now],
            ['id_plant' => '6', 'section' => 'S304', 'created_at' => $now],
            ['id_plant' => '6', 'section' => 'S306', 'created_at' => $now],
            ['id_plant' => '7', 'section' => 'S122', 'created_at' => $now],
            ['id_plant' => '7', 'section' => 'S305L', 'created_at' => $now],
            ['id_plant' => '7', 'section' => 'S305L-OE', 'created_at' => $now],
            ['id_plant' => '7', 'section' => 'S305S', 'created_at' => $now],
            ['id_plant' => '7', 'section' => 'S305S-COE', 'created_at' => $now],
        ];

        Section::truncate();
        Section::insert($data);

        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'SECTION',
            'log_description' => "SEED SECTION $count DATA",
        ]);
    }
}
