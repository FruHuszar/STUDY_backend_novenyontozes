<?php

declare(strict_types=1);

final class PlantResource
{
    public static function make(PlantWithSpecies $entry): array
    {
        $plant = $entry->getPlant();
        $species = $entry->getSpecies();

        $payload = [
            'id' => $plant->getId(),
            'name' => $plant->getNickname(),
            'location' => $plant->getLocation(),
            'nextWatering' => self::toIso8601($plant->getNextWatering()),
            'needsAttention' => $plant->needsAttention(),
            'note' => $plant->getNote(),
            'userId' => $plant->getUserId(),
            'speciesId' => $plant->getSpeciesId(),
            'wateringIntervalHours' => $plant->getWateringIntervalHours(),
        ];

        if ($species === null) {
            return $payload;
        }

        $serialized = SpeciesResource::make($species);

        return array_merge($payload, [
            'latin' => $serialized['latin'],
            'img' => $serialized['img'],
            'facts' => $serialized['facts'],
            'phases' => $serialized['phases'],
            'note' => $plant->getNote() ?? $serialized['note'],
            'wateringIntervalHours' => $plant->getWateringIntervalHours() ?? $serialized['wateringIntervalHours'],
        ]);
    }

    public static function collection(array $entries): array
    {
        return array_map(static fn (PlantWithSpecies $entry): array => self::make($entry), $entries);
    }

    private static function toIso8601(?string $value): ?string
    {
        return $value === null ? null : str_replace(' ', 'T', $value);
    }
}
