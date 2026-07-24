<?php

declare(strict_types=1);

final class WateringLogModel implements JsonSerializable
{
    public function __construct(
        private readonly ?int $id,
        private readonly ?string $wateredAt,
        private readonly ?int $amountMl,
        private readonly string $source,
        private readonly int $myPlantId
    ) {
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

    public function getWateredAt(): ?string
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
            'wateredAt' => $this->wateredAt !== null ? str_replace(' ', 'T', $this->wateredAt) : null,
            'amountMl' => $this->amountMl,
            'source' => $this->source,
            'plantId' => $this->myPlantId,
        ];
    }
}
