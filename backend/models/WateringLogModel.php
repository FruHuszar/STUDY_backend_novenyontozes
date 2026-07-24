<?php

declare(strict_types=1);

final class WateringLogModel implements JsonSerializable
{
    private ?int $id;
    private string $wateredAt;
    private ?int $amountMl;
    private string $source;
    private int $myPlantId;

    public function __construct(?int $id, string $wateredAt, ?int $amountMl, string $source, int $myPlantId)
    {
        $this->id = $id;
        $this->wateredAt = $wateredAt;
        $this->amountMl = $amountMl;
        $this->source = $source;
        $this->myPlantId = $myPlantId;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['watered_at'],
            $row['amount_ml'] !== null ? (int) $row['amount_ml'] : null,
            (string) $row['source'],
            (int) $row['my_plant_id']
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWateredAt(): string
    {
        return $this->wateredAt;
    }

    public function getAmountMl(): ?int
    {
        return $this->amountMl;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getMyPlantId(): int
    {
        return $this->myPlantId;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'wateredAt' => str_replace(' ', 'T', $this->wateredAt),
            'amountMl' => $this->amountMl,
            'source' => $this->source,
            'plantId' => $this->myPlantId,
        ];
    }
}
