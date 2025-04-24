<?php

namespace App\Service\Import\Database;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Import\CSV\CSVConstants;
use App\Service\Import\DTO\ProductDTO;
use App\Service\Import\ImportResult;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ProductImporter extends CSVConstants
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $em;

    public function __construct(
        ProductRepository      $productRepository,
        EntityManagerInterface $em
    )
    {
        $this->productRepository = $productRepository;
        $this->em = $em;
    }

    public function importProduct(ProductDTO $dto, ImportResult $result, bool $testMode): void
    {
        $product = $this->productRepository->findOneBy(['strProductCode' => $dto->getCode()]) ?? new Product();

        $product->setStrProductCode($dto->getCode());
        $product->setStrProductName($dto->getName());
        $product->setStrProductDesc($dto->getDescription());
        $product->setDtmAdded(new DateTime());
        $product->setStmTimestamp(new DateTime());

        if ($dto->getDiscontinued()) {
            $product->setDtmDiscontinued(new DateTime);
        }

        if (!$testMode) {
            $this->em->persist($product);
            $this->em->flush();
        }

        $result->incrementSuccessful();
    }
}