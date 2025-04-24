<?php

namespace App\Service\Import\DTO;

class ProductDTO
{
    const MIN_COST = 5;
    const MAX_COST = 1000;
    const MIN_STOCK = 10;
    private readonly string $code;
    private readonly string $name;
    private readonly string $description;
    private readonly int $stock;
    private readonly float $cost;
    private readonly bool $discontinued;
    private array $failedRules = [];

    public function __construct(string $code, string $name, string $description, int $stock, float $cost, bool $discontinued)
    {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->stock = $this->setStock($stock);
        $this->cost = $this->setCost($cost);
        $this->discontinued = $discontinued;
    }

    private function setCost(float $cost): float
    {
        if ($cost < self::MIN_COST) {
            $this->failedRules[] = 'Cost less then $5';
        }

        if ($cost > self::MAX_COST) {
            $this->failedRules[] = 'Cost more then $1000';
        }

        return $cost;
    }

    private function setStock(float $stock): int
    {
        if ($stock < self::MIN_STOCK) {
            $this->failedRules[] = 'Stock less than 10 qty';
        }

        return $stock;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function getDiscontinued(): bool
    {
        return $this->discontinued;
    }

    public function getFailedRules(): array
    {
        return $this->failedRules;
    }

    public function isDiscontinued(): bool
    {
        return $this->discontinued;
    }

    public function isMinCost(): bool
    {
        return $this->cost < self::MIN_COST;
    }

    public function isMaxCost(): bool
    {
        return $this->cost > self::MAX_COST;
    }

    public function isMinStock(): bool
    {
        return $this->stock < self::MIN_COST;
    }

    public function isValid(): bool
    {
        return count($this->failedRules) === 0;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'stock' => $this->stock,
            'cost' => $this->cost,
            'discontinued' => $this->discontinued,
            'failedRules' => $this->failedRules,
        ];
    }
}