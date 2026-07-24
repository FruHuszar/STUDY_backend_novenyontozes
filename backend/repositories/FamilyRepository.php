<?php

declare(strict_types=1);

final class FamilyRepository extends Repository implements FamilyRepositoryInterface
{
    protected function table(): string
    {
        return 'family';
    }

    protected function selection(): string
    {
        return 'id, name';
    }

    protected function hydrate(array $row): FamilyModel
    {
        return FamilyModel::fromRow($row);
    }

    public function findAll(): array
    {
        return $this->select('ORDER BY name');
    }

    public function findById(int $id): ?FamilyModel
    {
        return $this->selectOne('WHERE id = :id', ['id' => $id]);
    }
}
