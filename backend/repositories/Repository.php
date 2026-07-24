<?php

declare(strict_types=1);

abstract class Repository
{
    public function __construct(protected readonly PDO $connection)
    {
    }

    abstract protected function table(): string;

    abstract protected function selection(): string;

    abstract protected function hydrate(array $row): object;

    protected function source(): string
    {
        return $this->table();
    }

    protected function writableColumns(): array
    {
        return [];
    }

    public function exists(int $id): bool
    {
        return $this->run('SELECT 1 FROM ' . $this->table() . ' WHERE id = :id', ['id' => $id])->fetch() !== false;
    }

    public function delete(int $id): bool
    {
        return $this->run('DELETE FROM ' . $this->table() . ' WHERE id = :id', ['id' => $id])->rowCount() > 0;
    }

    protected function run(string $sql, array $parameters = []): PDOStatement
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }

    protected function select(string $clauses = '', array $parameters = []): array
    {
        $sql = 'SELECT ' . $this->selection() . ' FROM ' . $this->source() . ' ' . $clauses;

        return array_map(
            fn (array $row): object => $this->hydrate($row),
            $this->run($sql, $parameters)->fetchAll()
        );
    }

    protected function selectOne(string $clauses, array $parameters = []): ?object
    {
        return $this->select($clauses . ' LIMIT 1', $parameters)[0] ?? null;
    }

    protected function applyChanges(int $id, array $changes): void
    {
        $assignments = [];
        $parameters = ['id' => $id];

        foreach ($this->writableColumns() as $field => $column) {
            if (array_key_exists($field, $changes)) {
                $assignments[] = "{$column} = :{$column}";
                $parameters[$column] = $changes[$field];
            }
        }

        if ($assignments === []) {
            return;
        }

        $this->run(
            'UPDATE ' . $this->table() . ' SET ' . implode(', ', $assignments) . ' WHERE id = :id',
            $parameters
        );
    }

    protected function lastInsertId(): int
    {
        return (int) $this->connection->lastInsertId();
    }

    protected function placeholders(array $values): string
    {
        return implode(', ', array_fill(0, count($values), '?'));
    }
}
