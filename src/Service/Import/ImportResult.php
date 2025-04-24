<?php

namespace App\Service\Import;

class ImportResult
{
    private int $total = 0;
    private int $success = 0;
    private array $failed = [];

    public function incrementTotal(): void
    {
        $this->total++;
    }

    public function incrementSuccessful(): void
    {
        $this->success++;
    }

    public function addFailedRow(int $line, string $reason): void
    {
        $this->failed[$line][] = $reason;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function getFailedRows(): array
    {
        return $this->failed;
    }
}