<?php

namespace App\Tests\Service\DTO;

use App\Service\Import\DTO\ProductDTO;
use PHPUnit\Framework\TestCase;

class ProductDTOTest extends TestCase
{
    public function testProductDTOWithAllValidData(): void
    {
        $productDTO = new ProductDTO('P0002', 'TV', 'Home tv', 17, 55.25, true);

        $this->assertSame('P0002', $productDTO->getCode());
        $this->assertSame('TV', $productDTO->getName());
        $this->assertSame('Home tv', $productDTO->getDescription());
        $this->assertEquals(17, $productDTO->getStock());
        $this->assertEquals(55.25, $productDTO->getCost());
        $this->assertTrue($productDTO->getDiscontinued());
    }

    public function testProductDTOWithMinCost(): void
    {
        $productDTO = new ProductDTO('P001', 'TV', 'New product', 12, 4, false);

        $this->assertTrue($productDTO->isMinCost());
    }

    public function testProductDTOWithMaxCost(): void
    {
        $productDTO = new ProductDTO('P001', 'TV', 'New product', 12, 1001, false);

        $this->assertTrue($productDTO->isMaxCost());
    }

    public function testProductDTOWithMinStock(): void
    {
        $productDTO = new ProductDTO('P001', 'TV', 'New product', 4, 10, false);

        $this->assertTrue($productDTO->isMinStock());
    }

    public function testProductDTOWithValidData(): void
    {
        $productDTO = new ProductDTO('P001', 'TV', 'New product', 12, 100, true);

        $this->assertTrue($productDTO->isValid());
    }
}
