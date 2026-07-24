<?php

declare(strict_types=1);

final class SpeciesRepository
{
    private const COLUMNS = 's.id, s.name, s.latin_name, s.image_url, s.habitat, s.light_need,
                             s.watering_interval_hours, s.description, s.family_id, f.name AS family_name';

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query(
            'SELECT ' . self::COLUMNS . ' FROM species s JOIN family f ON f.id = s.family_id ORDER BY s.name'
        );

        return $this->attachPhases(array_map(
            static fn (array $row): SpeciesModel => SpeciesModel::fromRow($row),
            $statement->fetchAll()
        ));
    }

    public function findById(int $id): ?SpeciesModel
    {
        $statement = $this->connection->prepare(
            'SELECT ' . self::COLUMNS . ' FROM species s JOIN family f ON f.id = s.family_id WHERE s.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        $withPhases = $this->attachPhases([SpeciesModel::fromRow($row)]);

        return $withPhases[0];
    }

    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $statement = $this->connection->prepare(
            'SELECT ' . self::COLUMNS . " FROM species s JOIN family f ON f.id = s.family_id WHERE s.id IN ({$placeholders})"
        );
        $statement->execute(array_values($ids));

        $models = $this->attachPhases(array_map(
            static fn (array $row): SpeciesModel => SpeciesModel::fromRow($row),
            $statement->fetchAll()
        ));

        $indexed = [];

        foreach ($models as $model) {
            $indexed[$model->getId()] = $model;
        }

        return $indexed;
    }

    public function exists(int $id): bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM species WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch() !== false;
    }

    public function findBloomingInMonth(int $month): array
    {
        $statement = $this->connection->prepare(
            'SELECT ' . self::COLUMNS . '
             FROM species s
             JOIN family f ON f.id = s.family_id
             JOIN species_phase sp ON sp.species_id = s.id
             JOIN phase p ON p.id = sp.phase_id
             WHERE p.code = :code AND sp.month = :month
             ORDER BY s.name'
        );
        $statement->execute(['code' => 'blooming', 'month' => $month]);

        return $this->attachPhases(array_map(
            static fn (array $row): SpeciesModel => SpeciesModel::fromRow($row),
            $statement->fetchAll()
        ));
    }

    private function attachPhases(array $models): array
    {
        if ($models === []) {
            return [];
        }

        $ids = array_map(static fn (SpeciesModel $model): int => (int) $model->getId(), $models);
        $phases = $this->findPhasesBySpeciesIds($ids);

        return array_map(
            static fn (SpeciesModel $model): SpeciesModel => $model->withPhases($phases[$model->getId()] ?? []),
            $models
        );
    }

    private function findPhasesBySpeciesIds(array $ids): array
    {
        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $statement = $this->connection->prepare(
            "SELECT sp.species_id, p.code, sp.month
             FROM species_phase sp
             JOIN phase p ON p.id = sp.phase_id
             WHERE sp.species_id IN ({$placeholders})
             ORDER BY sp.month"
        );
        $statement->execute(array_values($ids));

        $grouped = [];

        foreach ($statement->fetchAll() as $row) {
            $grouped[(int) $row['species_id']][(string) $row['code']][] = (int) $row['month'];
        }

        return $grouped;
    }
}
