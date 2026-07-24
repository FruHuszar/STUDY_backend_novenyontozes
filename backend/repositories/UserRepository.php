<?php

declare(strict_types=1);

final class UserRepository
{
    private const COLUMNS = 'id, email, password_hash, name, notify_email, notify_push, created_at';

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query('SELECT ' . self::COLUMNS . ' FROM user ORDER BY id');

        return array_map(
            static fn (array $row): UserModel => UserModel::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $id): ?UserModel
    {
        $statement = $this->connection->prepare('SELECT ' . self::COLUMNS . ' FROM user WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : UserModel::fromRow($row);
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM user WHERE email = :email';
        $parameters = ['email' => $email];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetch() !== false;
    }

    public function create(UserModel $user): UserModel
    {
        $statement = $this->connection->prepare(
            'INSERT INTO user (email, password_hash, name, notify_email, notify_push)
             VALUES (:email, :password_hash, :name, :notify_email, :notify_push)'
        );

        $statement->execute([
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'name' => $user->getName(),
            'notify_email' => $user->isNotifyEmail() ? 1 : 0,
            'notify_push' => $user->isNotifyPush() ? 1 : 0,
        ]);

        return $this->findById((int) $this->connection->lastInsertId());
    }

    public function update(int $id, array $changes): ?UserModel
    {
        $allowed = ['email', 'password_hash', 'name', 'notify_email', 'notify_push'];
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

        $statement = $this->connection->prepare('UPDATE user SET ' . implode(', ', $assignments) . ' WHERE id = :id');
        $statement->execute($parameters);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM user WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
