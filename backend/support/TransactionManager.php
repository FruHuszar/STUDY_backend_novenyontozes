<?php

declare(strict_types=1);

final class TransactionManager implements TransactionManagerInterface
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function run(callable $work): mixed
    {
        $this->connection->beginTransaction();

        try {
            $result = $work();

            $this->connection->commit();
        } catch (Throwable $failure) {
            $this->connection->rollBack();

            throw $failure;
        }

        return $result;
    }
}
