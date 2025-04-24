<?php

namespace App\Tests\Service\Import\CSV;

use App\Exception\InvalidFileException;
use App\Service\Import\CSV\CSVConstants;
use App\Service\Import\CSV\CSVFileValidator;
use PHPUnit\Framework\TestCase;

class CSVFileValidatorTest extends TestCase
{
    private CSVFileValidator $validator;
    private string $testFile;

    protected function setUp(): void
    {
        $this->validator = new CSVFileValidator();
        $this->testFile = sys_get_temp_dir() . '/test.csv';

        $content = implode(',', CSVConstants::PRODUCT_HEADER_LIST) . PHP_EOL;
        $content .= "Code11,Product1,Desc1,1,22,yes";
        file_put_contents($this->testFile, $content);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testValidateFileWithValidFile(): void
    {
        $this->validator->validateFile($this->testFile);
        $this->assertTrue(true);
    }

    public function testValidateFileWIthInvalidHeaders(): void
    {
        $invalidFile = sys_get_temp_dir() . '/invalid_file.csv';
        file_put_contents($invalidFile, "Product Code,Name,Product Description,Cost in GBP,Discontinued");

        $this->expectException(InvalidFileException::class);
        $this->validator->validateFile($invalidFile);

        unlink($invalidFile);
    }
}
