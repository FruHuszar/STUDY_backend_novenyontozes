<?php

declare(strict_types=1);

final class SpeciesResource
{
    public static function make(SpeciesModel $species): array
    {
        return [
            'id' => $species->getId(),
            'name' => $species->getName(),
            'latin' => $species->getLatinName(),
            'img' => $species->getImageUrl(),
            'wateringIntervalHours' => $species->getWateringIntervalHours(),
            'note' => $species->getDescription(),
            'facts' => [
                ['label' => 'Family', 'value' => $species->getFamilyName()],
                ['label' => 'Habitat', 'value' => $species->getHabitat()],
                ['label' => 'Light', 'value' => $species->getLightNeed()],
            ],
            'phases' => $species->getPhases(),
        ];
    }

    public static function collection(array $species): array
    {
        return array_map(static fn (SpeciesModel $item): array => self::make($item), $species);
    }
}
