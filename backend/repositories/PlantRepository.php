<?php

declare(strict_types=1);

final class PlantRepository extends Repository implements PlantRepositoryInterface
{
    protected function table(): string
    {
        return 'my_plant';
    }

    protected function selection(): string
    {
        return 'id, nickname, location, watering_interval_hours, next_watering,
                needs_attention, note, created_at, user_id, species_id';
    }

    protected function hydrate(array $row): PlantModel
    {
        return PlantModel::fromRow($row);
    }

    protected function writableColumns(): array
    {
        return [
            'nickname' => 'nickname',
            'location' => 'location',
            'wateringIntervalHours' => 'watering_interval_hours',
            'needsAttention' => 'needs_attention',
            'note' => 'note',
            'speciesId' => 'species_id',
        ];
    }

    public function findAll(?int $userId = null): array
    {
        return $userId === null
            ? $this->select('ORDER BY next_watering')
            : $this->select('WHERE user_id = :user_id ORDER BY next_watering', ['user_id' => $userId]);
    }

    public function findDue(?int $userId = null): array
    {
        return $userId === null
            ? $this->select('WHERE next_watering <= NOW() ORDER BY next_watering')
            : $this->select(
                'WHERE next_watering <= NOW() AND user_id = :user_id ORDER BY next_watering',
                ['user_id' => $userId]
            );
    }

    public function findById(int $id): ?PlantModel
    {
        return $this->selectOne('WHERE id = :id', ['id' => $id]);
    }

    public function create(PlantModel $plant, int $intervalHours): PlantModel
    {
        $this->run(
            'INSERT INTO my_plant (nickname, location, watering_interval_hours, next_watering,
                                   needs_attention, note, user_id, species_id)
             VALUES (:nickname, :location, :watering_interval_hours,
                     DATE_ADD(NOW(), INTERVAL :interval HOUR), 0, :note, :user_id, :species_id)',
            [
                'nickname' => $plant->getNickname(),
                'location' => $plant->getLocation(),
                'watering_interval_hours' => $plant->getWateringIntervalHours(),
                'interval' => $intervalHours,
                'note' => $plant->getNote(),
                'user_id' => $plant->getUserId(),
                'species_id' => $plant->getSpeciesId(),
            ]
        );

        return $this->findById($this->lastInsertId());
    }

    public function update(int $id, array $changes): ?PlantModel
    {
        $this->applyChanges($id, $changes);

        return $this->findById($id);
    }

    public function resolveIntervalHours(int $id): ?int
    {
        $row = $this->run(
            'SELECT COALESCE(mp.watering_interval_hours, s.watering_interval_hours) AS interval_hours
             FROM my_plant mp
             JOIN species s ON s.id = mp.species_id
             WHERE mp.id = :id',
            ['id' => $id]
        )->fetch();

        return $row === false ? null : (int) $row['interval_hours'];
    }

    public function reschedule(int $id, int $intervalHours): void
    {
        $this->run(
            'UPDATE my_plant
             SET next_watering = DATE_ADD(NOW(), INTERVAL :interval HOUR), needs_attention = 0
             WHERE id = :id',
            ['interval' => $intervalHours, 'id' => $id]
        );
    }
}
