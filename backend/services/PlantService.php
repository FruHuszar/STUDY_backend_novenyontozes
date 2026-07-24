<?php

declare(strict_types=1);

final class PlantService
{
    public function __construct(
        private readonly PlantRepositoryInterface $plants,
        private readonly SpeciesRepositoryInterface $species,
        private readonly UserRepositoryInterface $users
    ) {
    }

    public function list(?int $userId): array
    {
        return $this->withSpecies($this->plants->findAll($userId));
    }

    public function listDue(?int $userId): array
    {
        return $this->withSpecies($this->plants->findDue($userId));
    }

    public function find(int $id): PlantWithSpecies
    {
        $plant = $this->plants->findById($id);

        if ($plant === null) {
            throw NotFoundException::resource('Plant');
        }

        return $this->withSpecies([$plant])[0];
    }

    public function create(array $data): PlantWithSpecies
    {
        if (!$this->users->exists($data['userId'])) {
            throw ValidationException::field('userId', 'This user does not exist.');
        }

        $species = $this->species->findById($data['speciesId']);

        if ($species === null) {
            throw ValidationException::field('speciesId', 'This species does not exist.');
        }

        $plant = $this->plants->create(
            new PlantModel(
                null,
                $data['nickname'],
                $data['location'],
                $data['wateringIntervalHours'],
                null,
                false,
                $data['note'],
                $data['userId'],
                $data['speciesId']
            ),
            $data['wateringIntervalHours'] ?? $species->getWateringIntervalHours()
        );

        return new PlantWithSpecies($plant, $species);
    }

    public function update(int $id, array $changes): PlantWithSpecies
    {
        if (!$this->plants->exists($id)) {
            throw NotFoundException::resource('Plant');
        }

        if (array_key_exists('speciesId', $changes) && !$this->species->exists($changes['speciesId'])) {
            throw ValidationException::field('speciesId', 'This species does not exist.');
        }

        $this->plants->update($id, $changes);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        if (!$this->plants->delete($id)) {
            throw NotFoundException::resource('Plant');
        }
    }

    private function withSpecies(array $plants): array
    {
        if ($plants === []) {
            return [];
        }

        $speciesIds = array_unique(array_map(
            static fn (PlantModel $plant): int => $plant->getSpeciesId(),
            $plants
        ));

        $species = $this->species->findByIds(array_values($speciesIds));

        return array_map(
            static fn (PlantModel $plant): PlantWithSpecies => new PlantWithSpecies(
                $plant,
                $species[$plant->getSpeciesId()] ?? null
            ),
            $plants
        );
    }
}
