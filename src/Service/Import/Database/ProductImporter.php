<?php

namespace App\Service\Import\Database;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Import\CSV\CSVConstants;
use App\Service\Import\DTO\ProductDTO;
use App\Service\Import\ImportHistoryLogger;
use App\Service\Import\ImportResult;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\EntityIdentityCollisionException;
use Psr\Log\LogLevel;

class ProductImporter extends CSVConstants
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $em;
    private ImportHistoryLogger $logger;

    public function __construct(
        ProductRepository      $productRepository,
        EntityManagerInterface $em,
        ImportHistoryLogger    $logger,
    )
    {
        $this->productRepository = $productRepository;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function importProduct(ProductDTO $dto, ImportResult $result, bool $testMode): void
    {
        //Update if the product already exists or create a new one
        $product = $this->productRepository->findOneBy(['strProductCode' => $dto->getCode()]) ?? new Product();
        try {
            $product->setStrProductCode($dto->getCode());
            $product->setStrProductName($dto->getName());
            $product->setStrProductDesc($dto->getDescription());
            $product->setDtmAdded(new DateTime());
            $product->setStmTimestamp(new DateTime());

            if ($dto->getDiscontinued()) {
                $product->markDiscontinued();
            }

            // If test mode is true without DB changes
            if (!$testMode) {
                $this->em->persist($product);
                $this->em->flush();
                $this->logger->log(LogLevel::INFO, "Imported " . $dto->getCode(), $dto->toArray());
            }
        } catch (EntityIdentityCollisionException $collisionException) {
            $this->logger->log(LogLevel::ERROR, $collisionException->getMessage(), $product);
        }

        $result->incrementSuccessful();
    }
}