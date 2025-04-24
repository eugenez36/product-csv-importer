<?php

namespace App\Service\Import\CSV;


use App\Exception\InvalidFileException;

class CSVFileValidator extends CSVConstants
{
    private const REQUIRED_HEADERS = [
        self::PRODUCT_CODE_HEADER,
        self::PRODUCT_NAME_HEADER,
        self::PRODUCT_DESCRIPTION_HEADER,
        self::PRODUCT_QUANTITY_HEADER,
        self::PRODUCT_PRICE_HEADER,
        self::PRODUCT_DISCONTINUED_HEADER,
    ];

    public function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidFileException("File does not exist: $filePath");
        }

        $handle = fopen($filePath, "r");
        if ($handle === false) {
            throw new InvalidFileException("Unable to open file: $filePath");
        }

        $headers = fgetcsv($handle);
        fclose($handle);

        if ($headers === false) {
            throw new InvalidFileException("Invalid file headers");
        }

        $missingHeaders = array_diff(self::REQUIRED_HEADERS, $headers);
        if (count($missingHeaders) > 0) {
            throw new InvalidFileException("Missing fields: " . implode(', ', $missingHeaders));
        }
    }
}