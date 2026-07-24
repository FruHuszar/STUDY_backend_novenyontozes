<?php

declare(strict_types=1);

final class PlantWithSpecies
{
    public function __construct(
        private readonly PlantModel $plant,
        private readonly ?SpeciesModel $species = null
    ) {
    }

    public function getPlant(): PlantModel
    {
        return $this->plant;
    }

    public function getSpecies(): ?SpeciesModel
    {
        return $this->species;
    }
}
