<?php

namespace App\Tests\Service\CSV;

use App\Service\Import\CSV\CSVConstants;
use App\Service\Import\CSV\CSVRowValidator;
use PHPUnit\Framework\TestCase;

class CSVRowValidatorTest extends TestCase
{
    private CSVRowValidator $validator;
    private array $headers;
    protected function setUp(): void
    {
        $this->validator = new CSVRowValidator();
        $this->headers = CSVConstants::PRODUCT_HEADER_LIST;
    }

    public function testValidateRowWithAllValidData(): void
    {
        $row = ['P0001', 'TV', 'Prod description', '10', '124.22', 'yes'];
        $result = $this->validator->validateRow($this->headers, $row);

        $this->assertEquals('P0001', $result['data'][0]);
        $this->assertEquals('TV', $result['data'][1]);
        $this->assertEquals('Prod description', $result['data'][2]);
        $this->assertEquals(10, $result['data'][3]);
        $this->assertEquals(124.22, $result['data'][4]);
        $this->assertEquals(1, $result['data'][5]);
    }

    public function testValidateRowWithAllInvalidData(): void
    {
        $row = ['', 'TV', 'Prod description', '1-0', '$124.22', 'yes'];
        $result = $this->validator->validateRow($this->headers, $row);

        $this->assertNotEmpty($result['errors']);
        $this->assertTrue(in_array("Field [" . CSVConstants::PRODUCT_CODE_HEADER . "] empty or null", $result['errors']));
        $this->assertTrue(in_array("Field [" . CSVConstants::PRODUCT_PRICE_HEADER . "] is not a number", $result['errors']));
        $this->assertTrue(in_array("Field [" . CSVConstants::PRODUCT_QUANTITY_HEADER . "] is not a number", $result['errors']));
    }

    public function testValidateRowWithInvalidPriceData(): void
    {
        $row = ['P0001', 'TV', 'Prod description', '10', 'D24.22', 'yes'];
        $result = $this->validator->validateRow($this->headers, $row);

        $this->assertEquals("Field [" . CSVConstants::PRODUCT_PRICE_HEADER . "] is not a number", $result['errors'][0]);
    }

    public function testValidateRowWithInvalidStockData(): void
    {
        $row = ['P0001', 'TV', 'Prod description', '', '100.10', 'yes'];
        $result = $this->validator->validateRow($this->headers, $row);

        $this->assertEquals("Field [" . CSVConstants::PRODUCT_QUANTITY_HEADER . "] is not a number", $result['errors'][0]);
    }

    public function testValidateRowWithEmptyProductCodeData(): void
    {
        $row = ['', 'TV', 'Prod description', '12', '133.10', 'yes'];
        $result = $this->validator->validateRow($this->headers, $row);

        $this->assertEquals("Field [" . CSVConstants::PRODUCT_CODE_HEADER . "] empty or null", $result['errors'][0]);
    }
}