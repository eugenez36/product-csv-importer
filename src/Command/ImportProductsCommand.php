<?php

namespace App\Command;

use App\Exception\InvalidDataException;
use App\Exception\InvalidFileException;
use App\Service\Import\CSV\CSVConstants;
use App\Service\Import\CSV\CSVFileValidator;
use App\Service\Import\CSV\CSVReader;
use App\Service\Import\CSV\CSVRowValidator;
use App\Service\Import\Database\ProductImporter;
use App\Service\Import\DTO\ProductDTO;
use App\Service\Import\ImportLogger;
use App\Service\Import\ImportResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-product',
    description: 'Import products from a CSV file',
)]
class ImportProductsCommand extends Command
{
    private CSVFileValidator $fileValidator;
    private CSVReader $csvReader;
    private CSVRowValidator $rowValidator;
    private ProductImporter $productImporter;
    private ImportLogger $importLogger;
    const fileArgument = 'file';
    const testOption = 'test';

    public function __construct(
        CSVFileValidator $fileValidator,
        CSVReader        $csvReader,
        CSVRowValidator  $rowValidator,
        ProductImporter  $productImporter,
        ImportLogger     $importLogger,
    )
    {
        parent::__construct();
        $this->fileValidator = $fileValidator;
        $this->csvReader = $csvReader;
        $this->rowValidator = $rowValidator;
        $this->productImporter = $productImporter;
        $this->importLogger = $importLogger;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::fileArgument, InputArgument::REQUIRED, 'Csv file path')
            ->addOption(self::testOption, 't', InputOption::VALUE_NONE, 'Run in test mode');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument(self::fileArgument);
        $testMode = $input->getOption(self::testOption);

        $result = new ImportResult();

        try {
            $this->fileValidator->validateFile($filePath);

            $headers = $this->getHeaders($filePath);

            foreach ($this->csvReader->readFile($filePath) as $line => $row) {
                try {
                    $item = $this->rowValidator->validateRow($headers, $row);
                    if (count($item['errors']) === 0) {
                        $productDTO = new ProductDTO(
                            $item['data'][CSVConstants::PRODUCT_CODE_HEADER],
                            $item['data'][CSVConstants::PRODUCT_NAME_HEADER],
                            $item['data'][CSVConstants::PRODUCT_DESCRIPTION_HEADER],
                            $item['data'][CSVConstants::PRODUCT_QUANTITY_HEADER],
                            $item['data'][CSVConstants::PRODUCT_PRICE_HEADER],
                            (bool)$item['data'][CSVConstants::PRODUCT_DISCONTINUED_HEADER]
                        );
                        if ($productDTO->isValid()) {
                            $this->productImporter->importProduct($productDTO, $result, $testMode);
                        } else {
                            foreach ($productDTO->getFailedRules() as $rule) {
                                $result->addFailedRow($line, $rule);
                            }
                        }
                    } else {
                        foreach ($item['errors'] as $error) {
                            $result->addFailedRow($line, $error);
                        }
                    }
                } catch (InvalidDataException $dataException) {
                    $result->addFailedRow($line, $dataException->getMessage());
                }
            }

            $io->success($this->importLogger->generateTotalHeaderString($result));
            if (count($result->getFailedRows()) !== 0) {
                $io->warning($this->importLogger->generateFailedRow($result));
            }

            return Command::SUCCESS;
        } catch (InvalidFileException $invalidFileException) {
            $io->error(['Import aborted', $invalidFileException->getMessage()]);
            return Command::FAILURE;
        }
    }

    private function getHeaders(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);
        fclose($handle);

        return $headers;
    }
}
