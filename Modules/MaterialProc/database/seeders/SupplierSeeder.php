<?php

namespace Modules\MaterialProc\Database\Seeders;

use App\Models\Log\LogTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\MaterialProc\Models\Supplier;

class SupplierSeeder extends Seeder
{
    // RM_Receiving.xlsx > Sheet Supplier (63 rows)
    public function run(): void
    {
        $now = Carbon::now();
        $supplierData = [
            ['supplier' => 'ADHITYA SERAYAKORITA, PT', 'created_at' => $now],
            ['supplier' => 'ANDES AGRO INVESTAMA, PT', 'created_at' => $now],
            ['supplier' => 'AWANA SAWIT LESTARI, PT.', 'created_at' => $now],
            ['supplier' => 'BANGUNJAYA ALAM PERMAI, PT', 'created_at' => $now],
            ['supplier' => 'BANYUASIN NUSANTARA SEJAHTERA, PT', 'created_at' => $now],
            ['supplier' => 'BATARA ELOK SEMESTA TERPADU, PT', 'created_at' => $now],
            ['supplier' => 'BERKAH EMAS SUMBER TERANG, PT', 'created_at' => $now],
            ['supplier' => 'BORNEO INDAH MARJAYA, PT', 'created_at' => $now],
            ['supplier' => 'BUANA KARYA BHAKTI, PT.', 'created_at' => $now],
            ['supplier' => 'BUDI NABATI PERKASA, PT', 'created_at' => $now],
            ['supplier' => 'BUKIT BINTANG SAWIT, PT.', 'created_at' => $now],
            ['supplier' => 'CARGILL TRADING INDONESIA, PT.', 'created_at' => $now],
            ['supplier' => 'CITRA BORNEO UTAMA TBK, PT', 'created_at' => $now],
            ['supplier' => 'FIRST LAMANDAU TIMBER INTERNATIONAL', 'created_at' => $now],
            ['supplier' => 'GAWI MAKMUR KALIMANTAN, PT', 'created_at' => $now],
            ['supplier' => 'GLOBAL INTERINTI INDUSTRY, PT', 'created_at' => $now],
            ['supplier' => 'GREEN GLOBAL UTAMA, PT.', 'created_at' => $now],
            ['supplier' => 'GUNUNG SEJAHTERA DUA INDAH, PT', 'created_at' => $now],
            ['supplier' => 'GUNUNG SEJAHTERA IBU PERTIWI, PT', 'created_at' => $now],
            ['supplier' => 'GUNUNG SEJAHTERA PUTI PESONA, PT', 'created_at' => $now],
            ['supplier' => 'HARTONO PLANTATION INDONESIA, PT', 'created_at' => $now],
            ['supplier' => 'HINDOLI, PT.', 'created_at' => $now],
            ['supplier' => 'JALIN VANEO, PT.', 'created_at' => $now],
            ['supplier' => 'KARYAINDAH ALAM SEJAHTERA, PT', 'created_at' => $now],
            ['supplier' => 'KARYANUSA EKADAYA, PT', 'created_at' => $now],
            ['supplier' => 'KODECO AGROJAYA MANDIRI, PT', 'created_at' => $now],
            ['supplier' => 'KURNIA TUNGGAL NUGRAHA, PT', 'created_at' => $now],
            ['supplier' => 'KUTAI REFINERY NUSANTARA, PT.', 'created_at' => $now],
            ['supplier' => 'LAGUNA MANDIRI, PT', 'created_at' => $now],
            ['supplier' => 'LANANG AGRO BERSATU, PT.', 'created_at' => $now],
            ['supplier' => 'LETAWA, PT', 'created_at' => $now],
            ['supplier' => 'MAYA AGRO INVESTAMA, PT.', 'created_at' => $now],
            ['supplier' => 'MEWAH OILS SDN BHD', 'created_at' => $now],
            ['supplier' => 'MITRA ANEKA REZEKI, PT.', 'created_at' => $now],
            ['supplier' => 'MITRA MENDAWAI SEJATI, PT', 'created_at' => $now],
            ['supplier' => 'NUSARAYA PERMAI, PT.', 'created_at' => $now],
            ['supplier' => 'PACRIM NUSANTARA LESTARI FOODS, PT.', 'created_at' => $now],
            ['supplier' => 'PALMA MAS SEJATI, PT.', 'created_at' => $now],
            ['supplier' => 'PARNA AGROMAS, PT.', 'created_at' => $now],
            ['supplier' => 'PASANG KAYU, PT', 'created_at' => $now],
            ['supplier' => 'PENITI SUNGAI PURUN, PT', 'created_at' => $now],
            ['supplier' => 'PERKEBUNAN NUSANTARA IV, PT.', 'created_at' => $now],
            ['supplier' => 'PERKEBUNAN NUSANTARA VII, PT', 'created_at' => $now],
            ['supplier' => 'POLIPLANT SEJAHTERA, PT.', 'created_at' => $now],
            ['supplier' => 'PRIMA SUKSES SEJAHTERA ABADI, PT.', 'created_at' => $now],
            ['supplier' => 'SARI DUMAI SEJATI, PT', 'created_at' => $now],
            ['supplier' => 'SAWIT SUMBERMAS SARANA Tbk, PT', 'created_at' => $now],
            ['supplier' => 'SINAR BENGKULU INTI MULYA, PT', 'created_at' => $now],
            ['supplier' => 'SINAR JAYA INTI MULYA, PT', 'created_at' => $now],
            ['supplier' => 'SINAR SAWIT SENTOSA, PT', 'created_at' => $now],
            ['supplier' => 'SINAR TAYAN INTI MULYA, PT', 'created_at' => $now],
            ['supplier' => 'SINAR TENGGARONG INTI MULYA, PT.', 'created_at' => $now],
            ['supplier' => 'SUKSES KARYA MANDIRI, PT.', 'created_at' => $now],
            ['supplier' => 'SURYAMAS CIPTA PERKASA, PT', 'created_at' => $now],
            ['supplier' => 'SURYARAYA LESTARI, PT', 'created_at' => $now],
            ['supplier' => 'SWADAYA MUKTI PRAKARSA, PT', 'created_at' => $now],
            ['supplier' => 'SYNERGY OIL NUSANTARA, PT', 'created_at' => $now],
            ['supplier' => 'TECKNO DUA INDONESIA, PT.', 'created_at' => $now],
            ['supplier' => 'TEGUH SEMPURNA, PT', 'created_at' => $now],
            ['supplier' => 'TUNAS AGRO SUBUR KENCANA, PT.', 'created_at' => $now],
            ['supplier' => 'TUNAS BARU LAMPUNG TBK, PT', 'created_at' => $now],
            ['supplier' => 'WANASAWIT SUBUR LESTARI, PT', 'created_at' => $now],
            ['supplier' => 'WARU KALTIM PLANTATION, PT', 'created_at' => $now],
        ];

        Supplier::truncate();
        Supplier::insert($supplierData);

        $supplierCount = count($supplierData);
        LogTransaction::insert([
            'log_module' => 'MaterialProc',
            'log_type' => 'SEED',
            'log_model' => 'SUPPLIER',
            'log_description' => "SEED SUPPLIER $supplierCount DATA",
        ]);
    }
}
