<?php

namespace App\Tests\Service\Import\CSV;

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

        $this->assertEquals('P0001', $result['data'][CSVConstants::PRODUCT_CODE_HEADER]);
        $this->assertEquals('TV', $result['data'][CSVConstants::PRODUCT_NAME_HEADER]);
        $this->assertEquals('Prod description', $result['data'][CSVConstants::PRODUCT_DESCRIPTION_HEADER]);
        $this->assertEquals(10, $result['data'][CSVConstants::PRODUCT_QUANTITY_HEADER]);
        $this->assertEquals(124.22, $result['data'][CSVConstants::PRODUCT_PRICE_HEADER]);
        $this->assertEquals(1, $result['data'][CSVConstants::PRODUCT_DISCONTINUED_HEADER]);
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

    public function testValidateRowWithCombineMethod(): void
    {
        $row = ['P011', 'TV', 'TV description', '11', '111.111', ''];
        $result = $this->validator->validateRow($this->headers, $row);

        $this->assertArrayHasKey(CSVConstants::PRODUCT_CODE_HEADER, $result['data']);
        $this->assertArrayHasKey(CSVConstants::PRODUCT_NAME_HEADER, $result['data']);
        $this->assertArrayHasKey(CSVConstants::PRODUCT_DESCRIPTION_HEADER, $result['data']);
        $this->assertArrayHasKey(CSVConstants::PRODUCT_QUANTITY_HEADER, $result['data']);
        $this->assertArrayHasKey(CSVConstants::PRODUCT_CODE_HEADER, $result['data']);
    }
}