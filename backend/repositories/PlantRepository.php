<?php

declare(strict_types=1);

final class PlantRepository
{
    private const COLUMNS = 'id, nickname, location, watering_interval_hours, next_watering,
                             needs_attention, note, created_at, user_id, species_id';

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query('SELECT ' . self::COLUMNS . ' FROM my_plant ORDER BY next_watering');

        return $this->mapAll($statement->fetchAll());
    }

    public function findAllByUser(int $userId): array
    {
        $statement = $this->connection->prepare(
            'SELECT ' . self::COLUMNS . ' FROM my_plant WHERE user_id = :user_id ORDER BY next_watering'
        );
        $statement->execute(['user_id' => $userId]);

        return $this->mapAll($statement->fetchAll());
    }

    public function findDue(): array
    {
        $statement = $this->connection->query(
            'SELECT ' . self::COLUMNS . ' FROM my_plant WHERE next_watering <= NOW() ORDER BY next_watering'
        );

        return $this->mapAll($statement->fetchAll());
    }

    public function findById(int $id): ?MyPlantModel
    {
        $statement = $this->connection->prepare('SELECT ' . self::COLUMNS . ' FROM my_plant WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : MyPlantModel::fromRow($row);
    }

    public function create(MyPlantModel $plant, int $intervalHours): MyPlantModel
    {
        $statement = $this->connection->prepare(
            'INSERT INTO my_plant (nickname, location, watering_interval_hours, next_watering, needs_attention, note, user_id, species_id)
             VALUES (:nickname, :location, :watering_interval_hours, DATE_ADD(NOW(), INTERVAL :interval HOUR), 0, :note, :user_id, :species_id)'
        );

        $statement->execute([
            'nickname' => $plant->getNickname(),
            'location' => $plant->jsonSerialize()['location'],
            'watering_interval_hours' => $plant->getWateringIntervalHours(),
            'interval' => $intervalHours,
            'note' => $plant->jsonSerialize()['note'],
            'user_id' => $plant->getUserId(),
            'species_id' => $plant->getSpeciesId(),
        ]);

        return $this->findById((int) $this->connection->lastInsertId());
    }

    public function update(int $id, array $changes): ?MyPlantModel
    {
        $allowed = ['nickname', 'location', 'watering_interval_hours', 'next_watering', 'needs_attention', 'note', 'species_id'];
        $assignments = [];
        $parameters = ['id' => $id];

        foreach ($allowed as $column) {
            if (array_key_exists($column, $changes)) {
                $assignments[] = "{$column} = :{$column}";
                $parameters[$column] = $changes[$column];
            }
        }

        if ($assignments === []) {
            return $this->findById($id);
        }

        $statement = $this->connection->prepare('UPDATE my_plant SET ' . implode(', ', $assignments) . ' WHERE id = :id');
        $statement->execute($parameters);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM my_plant WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    public function resolveIntervalHours(int $id): ?int
    {
        $statement = $this->connection->prepare(
            'SELECT COALESCE(mp.watering_interval_hours, s.watering_interval_hours) AS interval_hours
             FROM my_plant mp
             JOIN species s ON s.id = mp.species_id
             WHERE mp.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : (int) $row['interval_hours'];
    }

    private function mapAll(array $rows): array
    {
        return array_map(
            static fn (array $row): MyPlantModel => MyPlantModel::fromRow($row),
            $rows
        );
    }
}
