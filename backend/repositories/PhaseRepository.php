<?php

declare(strict_types=1);

final class PhaseRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query('SELECT id, code, label, icon FROM phase ORDER BY id');

        return array_map(
            static fn (array $row): PhaseModel => PhaseModel::fromRow($row),
            $statement->fetchAll()
        );
    }
}
