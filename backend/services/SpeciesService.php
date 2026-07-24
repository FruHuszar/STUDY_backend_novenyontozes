<?php

declare(strict_types=1);

final class SpeciesService
{
    public function __construct(
        private readonly SpeciesRepositoryInterface $species,
        private readonly FamilyRepositoryInterface $families,
        private readonly PhaseRepositoryInterface $phases
    ) {
    }

    public function list(?int $bloomingIn): array
    {
        if ($bloomingIn === null) {
            return $this->species->findAll();
        }

        if ($bloomingIn < 1 || $bloomingIn > 12) {
            throw ValidationException::field('bloomingIn', 'This field must be a month between 1 and 12.');
        }

        return $this->species->findBloomingInMonth($bloomingIn);
    }

    public function find(int $id): SpeciesModel
    {
        $species = $this->species->findById($id);

        if ($species === null) {
            throw NotFoundException::resource('Species');
        }

        return $species;
    }

    public function families(): array
    {
        return $this->families->findAll();
    }

    public function phases(): array
    {
        return $this->phases->findAll();
    }
}
