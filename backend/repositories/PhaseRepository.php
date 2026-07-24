<?php

declare(strict_types=1);

final class PhaseRepository extends Repository implements PhaseRepositoryInterface
{
    protected function table(): string
    {
        return 'phase';
    }

    protected function selection(): string
    {
        return 'id, code, label, icon';
    }

    protected function hydrate(array $row): PhaseModel
    {
        return PhaseModel::fromRow($row);
    }

    public function findAll(): array
    {
        return $this->select('ORDER BY id');
    }
}
