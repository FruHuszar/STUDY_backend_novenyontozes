<?php

declare(strict_types=1);

final class SpeciesRepository extends Repository implements SpeciesRepositoryInterface
{
    protected function table(): string
    {
        return 'species';
    }

    protected function source(): string
    {
        return 'species s JOIN family f ON f.id = s.family_id';
    }

    protected function selection(): string
    {
        return 's.id, s.name, s.latin_name, s.image_url, s.habitat, s.light_need,
                s.watering_interval_hours, s.description, s.family_id, f.name AS family_name';
    }

    protected function hydrate(array $row): SpeciesModel
    {
        return SpeciesModel::fromRow($row);
    }

    public function findAll(): array
    {
        return $this->withPhases($this->select('ORDER BY s.name'));
    }

    public function findById(int $id): ?SpeciesModel
    {
        $species = $this->select('WHERE s.id = :id LIMIT 1', ['id' => $id]);

        return $this->withPhases($species)[0] ?? null;
    }

    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $species = $this->withPhases(
            $this->select('WHERE s.id IN (' . $this->placeholders($ids) . ')', array_values($ids))
        );

        $indexed = [];

        foreach ($species as $item) {
            $indexed[$item->getId()] = $item;
        }

        return $indexed;
    }

    public function findBloomingInMonth(int $month): array
    {
        return $this->withPhases($this->select(
            'JOIN species_phase sp ON sp.species_id = s.id
             JOIN phase p ON p.id = sp.phase_id
             WHERE p.code = :code AND sp.month = :month
             ORDER BY s.name',
            ['code' => 'blooming', 'month' => $month]
        ));
    }

    private function withPhases(array $species): array
    {
        if ($species === []) {
            return [];
        }

        $phases = $this->findPhases(array_map(static fn (SpeciesModel $item): int => (int) $item->getId(), $species));

        return array_map(
            static fn (SpeciesModel $item): SpeciesModel => $item->withPhases($phases[$item->getId()] ?? []),
            $species
        );
    }

    private function findPhases(array $speciesIds): array
    {
        $rows = $this->run(
            'SELECT sp.species_id, p.code, sp.month
             FROM species_phase sp
             JOIN phase p ON p.id = sp.phase_id
             WHERE sp.species_id IN (' . $this->placeholders($speciesIds) . ')
             ORDER BY sp.month',
            array_values($speciesIds)
        )->fetchAll();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[(int) $row['species_id']][(string) $row['code']][] = (int) $row['month'];
        }

        return $grouped;
    }
}
