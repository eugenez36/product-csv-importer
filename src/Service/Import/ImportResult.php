<?php

namespace App\Service\Import;

class ImportResult
{
    private int $total = 0;
    private int $success = 0;
    private array $failed = [];

    private function incrementTotal(): void
    {
        $this->total++;
    }

    public function incrementSuccessful(): void
    {
        $this->success++;
        $this->incrementTotal();
    }

    public function addFailedRow(int $line, string $reason): void
    {
        //If there is no such line, then plus, if there is, add only the error
        if (!isset($this->failed[$line])) {
            $this->incrementTotal();
        }
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