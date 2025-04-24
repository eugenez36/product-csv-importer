<?php

namespace App\Tests\Service;

use App\Service\Import\ImportResult;
use PHPUnit\Framework\TestCase;

class ImportLoggerResultTest extends TestCase
{
    private array $failedRow1 = [
        'line' => 1,
        'reason' => 'Error with field',
    ];

    private array $failedRow2 = [
        'line' => 12,
        'reason' => 'Error with cost field',
    ];

    public function testImportLoggerResultWithData(): void
    {
        $logger = new ImportResult();
        $logger->incrementTotal();
        $logger->incrementTotal();
        $logger->incrementTotal();
        $logger->incrementTotal();
        $logger->incrementSuccessful();
        $logger->incrementSuccessful();
        $logger->addFailedRow($this->failedRow1['line'], $this->failedRow1['reason']);
        $logger->addFailedRow($this->failedRow2['line'], $this->failedRow2['reason']);

        $this->assertEquals(4, $logger->getTotal());
        $this->assertEquals(2, $logger->getSuccess());
        $this->assertEquals($this->failedRow1, $logger->getFailedRows()[0]);
        $this->assertEquals($this->failedRow2, $logger->getFailedRows()[1]);
    }
}