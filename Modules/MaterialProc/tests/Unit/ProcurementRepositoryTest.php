<?php

namespace Modules\MaterialProc\Tests\Unit;

use App\Traits\RefreshModuleDatabase;
use Illuminate\Support\Facades\DB;
use Modules\MaterialProc\Models\Procurement;
use Modules\MaterialProc\Repositories\Implementations\ProcurementRepository;
use Tests\TestCase;

class ProcurementRepositoryTest extends TestCase
{
    use RefreshModuleDatabase;

    protected ProcurementRepository $repository;
    private $table = 'procurement_ts';

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshModuleDatabase('MaterialProc');
        DB::statement('CREATE TABLE IF NOT EXISTS material_master (id INTEGER PRIMARY KEY, material_description TEXT, deleted_at TIMESTAMP)');
        DB::statement('CREATE TABLE IF NOT EXISTS plant_master (id INTEGER PRIMARY KEY, description TEXT, deleted_at TIMESTAMP)');

        $this->repository = new ProcurementRepository();
    }

    public function test_query()
    {
        $size = 5;
        $model = Procurement::factory()->count($size)->create();
        $query = $this->repository->query()->get();

        $this->assertDatabaseCount($this->table, $size);
        $this->assertEquals([1, 2, 3, 4, 5], $query->pluck('id')->toArray());
    }

    public function test_all()
    {
        $size = 5;
        $model = Procurement::factory()->count($size)->create();
        $query = $this->repository->all();

        $this->assertDatabaseCount($this->table, $size);
        $this->assertEquals([1, 2, 3, 4, 5], $query->pluck('id')->toArray());
    }

    public function test_find_model_by_id()
    {
        $model = Procurement::factory()->create();
        $found = $this->repository->findById($model->id);

        $this->assertNotNull($found);
        $this->assertEquals($model->id, $found->id);
    }


    public function test_create_model()
    {
        $data = [
            'id_supplier' => 1,
            'id_material' => 1,
            'id_plant' => 1,
            'qty' => 1000,
            'qty_plan' => 1000,
            'eta' => '2025-01-01',
            'eta_plan' => '2025-01-01',
        ];
        $model = $this->repository->create($data);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseHas($this->table, [
            'id_supplier' => 1,
            'id_material' => 1,
            'id_plant' => 1,
            'qty' => 1000,
            'qty_plan' => 1000,
            'eta' => '2025-01-01',
            'eta_plan' => '2025-01-01',
        ]);
        $this->assertEquals('1000', $model->qty);
        $this->assertEquals('2025-01-01', $model->eta);
    }

    public function test_update_model()
    {
        $model = Procurement::factory()->create();
        $updated = $this->repository->update($model->id, ['qty_actual' => 2000]);

        $this->assertNotNull($updated);
        $this->assertNotEquals($model->qty_actual, $updated->qty_actual);
        $this->assertDatabaseHas($this->table, [
            'id' => $model->id,
            'qty_actual' => 2000,
        ]);
    }

    public function test_delete_model()
    {
        $model = Procurement::factory()->create();
        $deleted = $this->repository->delete($model->id);

        $this->assertDatabaseCount($this->table, 0);
    }
}
