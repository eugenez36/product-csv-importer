<?php

namespace App\Tests\Service\Import;

use App\Service\Import\ImportLogger;
use App\Service\Import\ImportResult;
use PHPUnit\Framework\TestCase;

class ImportLoggerTest extends TestCase
{
    /**
     * Several errors in one line.
     * @return void
     */
    public function testImportLoggerGenerateTotalHeaderString(): void
    {
        $content = "Import completed. Total: 5, Successful: 2, Failed: 3";

        $result = new ImportResult();
        $logger = new ImportLogger();

        $result->incrementSuccessful();
        $result->incrementSuccessful();
        $result->addFailedRow('1', 'Field [Stock] is not a number');
        $result->addFailedRow('2', 'Row has incorrect number of columns');
        $result->addFailedRow('2', 'Field [Stock] is not a number');
        $result->addFailedRow('2', 'Stock less than 10 qty');
        $result->addFailedRow('112', 'Field [Stock] is not a number');

        $this->assertStringContainsString($content, $logger->generateTotalHeaderString($result));
    }

    public function testImportLoggerGenerateFailedRows(): void
    {
        $rowContent = "-Line[1]: |Field [Stock] is not a number|\n-Line[2]: |Row has incorrect number of columns|";
        $result = new ImportResult();
        $logger = new ImportLogger();

        $result->addFailedRow('1', 'Field [Stock] is not a number');
        $result->addFailedRow('2', 'Row has incorrect number of columns');

        $this->assertStringContainsString($rowContent, $logger->generateFailedRow($result));
    }
}