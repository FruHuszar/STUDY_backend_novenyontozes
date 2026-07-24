<?php

declare(strict_types=1);

final class PhaseModel implements JsonSerializable
{
    private ?int $id;
    private string $code;
    private string $label;
    private ?string $icon;

    public function __construct(?int $id, string $code, string $label, ?string $icon)
    {
        $this->id = $id;
        $this->code = $code;
        $this->label = $label;
        $this->icon = $icon;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['code'],
            (string) $row['label'],
            $row['icon'] !== null ? (string) $row['icon'] : null
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'label' => $this->label,
            'icon' => $this->icon,
        ];
    }
}
