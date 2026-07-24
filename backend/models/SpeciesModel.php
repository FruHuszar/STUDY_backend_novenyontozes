<?php

declare(strict_types=1);

final class SpeciesModel implements JsonSerializable
{
    private ?int $id;
    private string $name;
    private string $latinName;
    private ?string $imageUrl;
    private ?string $habitat;
    private ?string $lightNeed;
    private int $wateringIntervalHours;
    private ?string $description;
    private int $familyId;
    private ?string $familyName;
    private array $phases;

    public function __construct(
        ?int $id,
        string $name,
        string $latinName,
        ?string $imageUrl,
        ?string $habitat,
        ?string $lightNeed,
        int $wateringIntervalHours,
        ?string $description,
        int $familyId,
        ?string $familyName = null,
        array $phases = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->latinName = $latinName;
        $this->imageUrl = $imageUrl;
        $this->habitat = $habitat;
        $this->lightNeed = $lightNeed;
        $this->wateringIntervalHours = $wateringIntervalHours;
        $this->description = $description;
        $this->familyId = $familyId;
        $this->familyName = $familyName;
        $this->phases = $phases;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['latin_name'],
            $row['image_url'] !== null ? (string) $row['image_url'] : null,
            $row['habitat'] !== null ? (string) $row['habitat'] : null,
            $row['light_need'] !== null ? (string) $row['light_need'] : null,
            (int) $row['watering_interval_hours'],
            $row['description'] !== null ? (string) $row['description'] : null,
            (int) $row['family_id'],
            isset($row['family_name']) ? (string) $row['family_name'] : null
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWateringIntervalHours(): int
    {
        return $this->wateringIntervalHours;
    }

    public function getFamilyId(): int
    {
        return $this->familyId;
    }

    public function withPhases(array $phases): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->latinName,
            $this->imageUrl,
            $this->habitat,
            $this->lightNeed,
            $this->wateringIntervalHours,
            $this->description,
            $this->familyId,
            $this->familyName,
            $phases
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latin' => $this->latinName,
            'img' => $this->imageUrl,
            'wateringIntervalHours' => $this->wateringIntervalHours,
            'note' => $this->description,
            'facts' => [
                ['label' => 'Family', 'value' => $this->familyName],
                ['label' => 'Habitat', 'value' => $this->habitat],
                ['label' => 'Light', 'value' => $this->lightNeed],
            ],
            'phases' => $this->phases,
        ];
    }
}
