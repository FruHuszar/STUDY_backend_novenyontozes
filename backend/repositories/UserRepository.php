<?php

declare(strict_types=1);

final class UserRepository extends Repository implements UserRepositoryInterface
{
    protected function table(): string
    {
        return 'user';
    }

    protected function selection(): string
    {
        return 'id, email, password_hash, name, notify_email, notify_push, created_at';
    }

    protected function hydrate(array $row): UserModel
    {
        return UserModel::fromRow($row);
    }

    protected function writableColumns(): array
    {
        return [
            'email' => 'email',
            'passwordHash' => 'password_hash',
            'name' => 'name',
            'notifyEmail' => 'notify_email',
            'notifyPush' => 'notify_push',
        ];
    }

    public function findAll(): array
    {
        return $this->select('ORDER BY id');
    }

    public function findById(int $id): ?UserModel
    {
        return $this->selectOne('WHERE id = :id', ['id' => $id]);
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM user WHERE email = :email';
        $parameters = ['email' => $email];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        return $this->run($sql, $parameters)->fetch() !== false;
    }

    public function create(UserModel $user): UserModel
    {
        $this->run(
            'INSERT INTO user (email, password_hash, name, notify_email, notify_push)
             VALUES (:email, :password_hash, :name, :notify_email, :notify_push)',
            [
                'email' => $user->getEmail(),
                'password_hash' => $user->getPasswordHash(),
                'name' => $user->getName(),
                'notify_email' => $user->isNotifyEmail() ? 1 : 0,
                'notify_push' => $user->isNotifyPush() ? 1 : 0,
            ]
        );

        return $this->findById($this->lastInsertId());
    }

    public function update(int $id, array $changes): ?UserModel
    {
        $this->applyChanges($id, $changes);

        return $this->findById($id);
    }
}
