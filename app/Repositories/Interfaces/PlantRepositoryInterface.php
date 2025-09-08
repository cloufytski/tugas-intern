<?php

namespace App\Repositories\Interfaces;

interface PlantRepositoryInterface extends MasterRepositoryInterface
{
    public function firstByPlant(string $plant);
    public function firstByDescription(string $description);
}
