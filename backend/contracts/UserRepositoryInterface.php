<?php

declare(strict_types=1);

interface UserRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?UserModel;

    public function emailExists(string $email, ?int $exceptId = null): bool;

    public function create(UserModel $user): UserModel;

    public function update(int $id, array $changes): ?UserModel;

    public function delete(int $id): bool;

    public function exists(int $id): bool;
}
