<?php

namespace Tests\Unit;

use App\Models\Master\Plant;
use App\Repositories\Implementations\PlantRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PlantRepository $repository;
    private $table = 'plant_master';

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PlantRepository();
    }

    public function test_first_by_plant()
    {
        $plant = Plant::factory()->create(['plant' => '1']);
        $result = $this->repository->firstByPlant('1');

        $this->assertNotNull($result);
        $this->assertEquals($plant->id, $result->id);
        $this->assertEquals($plant->plant, $result->plant);
    }

    public function test_first_by_description()
    {
        $plant = Plant::factory()->create(['description' => 'test1']);
        $result = $this->repository->firstByDescription('test1');

        $this->assertNotNull($result);
        $this->assertEquals($plant->id, $result->id);
        $this->assertEquals($plant->description, $result->description);
    }

    public function test_query_includes_soft_deleted_records()
    {
        $plant = Plant::factory()->create();
        $plant->delete();

        $query = $this->repository->query()->get();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertTrue($query->contains('id', $plant->id));
    }

    public function test_all_order_by_id()
    {
        $plantA = Plant::factory()->create(['id' => 2]);
        $plantB = Plant::factory()->create(['id' => 1]);

        $result = $this->repository->all()->get();

        $this->assertDatabaseCount($this->table, 2);
        $this->assertEquals(2, $result->count());
        $this->assertEquals([1, 2], $result->pluck('id')->toArray());
    }

    public function test_create_model()
    {
        $data = [
            'plant' => '1',
            'description' => 'TEST',
        ];
        $model = $this->repository->create($data);
        $this->assertDatabaseHas($this->table, [
            'plant' => '1',
            'description' => 'TEST',
        ]);
        $this->assertEquals('TEST', $model->description);
    }

    public function test_find_model_by_id()
    {
        $model = Plant::factory()->create();
        $found = $this->repository->findById($model->id);

        $this->assertNotNull($found);
        $this->assertEquals($model->id, $found->id);
    }

    public function test_update_model()
    {
        $model = Plant::factory()->create();
        $updated = $this->repository->update($model->id, ['plant' => 'Update Plant']);

        $this->assertNotNull($updated);
        $this->assertNotEquals($model->plant, $updated->plant);
        $this->assertDatabaseHas($this->table, [
            'id' => $model->id,
            'plant' => 'Update Plant',
        ]);
    }

    public function test_delete_model()
    {
        $model = Plant::factory()->create();
        $deleted = $this->repository->delete($model->id);

        $this->assertNotNull($deleted);
        $this->assertSoftDeleted($model);
    }

    public function test_restore_model()
    {
        $model = Plant::factory()->create();
        $this->repository->delete($model->id);
        $restored = $this->repository->restore($model->id);

        $this->assertNotNull($restored);
        $this->assertNotSoftDeleted($restored);
    }
}
