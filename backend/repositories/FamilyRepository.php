<?php

declare(strict_types=1);

final class FamilyRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query('SELECT id, name FROM family ORDER BY name');

        return array_map(
            static fn (array $row): FamilyModel => FamilyModel::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $id): ?FamilyModel
    {
        $statement = $this->connection->prepare('SELECT id, name FROM family WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : FamilyModel::fromRow($row);
    }
}
