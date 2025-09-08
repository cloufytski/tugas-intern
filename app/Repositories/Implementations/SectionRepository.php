<?php

namespace App\Repositories\Implementations;

use App\Models\Master\Section;
use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Traits\LogTransactionTrait;

class SectionRepository implements SectionRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'MasterData';
    protected $modelName = 'SECTION';

    public function firstBySection(string $section)
    {
        return Section::firstWhere('section', '=', $section);
    }

    public function findByPlantArray(array $plantArray)
    {
        return Section::with('plant')
            ->whereIn('id_plant', $plantArray)
            ->get();
    }

    public function findByPlant(string $plantId)
    {
        return Section::with('plant')
            ->where('id_plant', '=', $plantId)
            ->orderBy('id')
            ->get();
    }

    public function query()
    {
        return Section::query()->with('plant')
            ->withTrashed();
    }

    public function all()
    {
        return Section::query()->with('plant')
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return Section::with('plant')->findOrFail($id);
    }

    public function create(array $data)
    {
        $model = Section::create($data);
        $this->log(logType: 'CREATE', model: $model, data: $data);
        return $model;
    }

    public function update(int $id, array $data)
    {
        $model = $this->findById($id);
        $this->log(logType: 'UPDATE', model: $model, data: $data);
        $model->update($data);
        return $model->load('plant');
    }

    public function delete(int $id)
    {
        $model = $this->findById($id);
        $model->delete();
        $this->log(logType: 'DELETE', model: $model, data: $id);
        return $model;
    }

    public function restore(int $id)
    {
        $model = Section::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }
}
