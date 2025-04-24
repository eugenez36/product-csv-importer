<?php

namespace App\Service\Import;

use SplFileObject;

class ScvReaderService implements \IteratorAggregate
{
    const ROW_NUMBER = 6;
//    private array $keys = [
//        'Product Code',
//        'Product Name',
//        'Product Description',
//        'Stock',
//        'Cost in GBP',
//        'Discontinued'
//    ];

    /**
     * @throws \Exception
     */
    public function __construct(
        private readonly string $filePath,
        private readonly string $delimiter = ',',
        private readonly string $enclosure = '"',
        private readonly string $escape = '\\',
    )
    {
        $this->validateFile();
    }

    /**
     * @throws \Exception
     */
    private function validateFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception("File {$this->filePath} does not exist");
        }
    }

    public function getIterator(): \Generator
    {
//        $reader = Reader::createFromPath($this->filePath);
//        $reader->setDelimiter($this->delimiter);
//        $reader->setEnclosure($this->enclosure);
//        $reader->setEscape($this->escape);
////        $reader->addStreamFilter('convert.iconv.ISO-8859-1/UTF-8');
//        $reader->setHeaderOffset(0);
//
//        $csv = fgetcsv(fopen($this->filePath, 'r'), 0, $this->delimiter, $this->enclosure, $this->escape);
//
//

        $file = new SplFileObject($this->filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);

        foreach ($file as $key => $row) {
            if ($key === 0) continue; // skip headers
            if ($row === [null]) continue; // skip empty row

            $rowData = [
                'line' => $key,
                'data' => $row,
                'errors' => [
                    $this->validateNumberColumns($row),

                ],
            ];

//            $rowData = $this->prepareData((array)$row, $key);
//            $rowData = $this->validateNumberColumns($rowData);
            yield $this->processRow($this->processRow($rowData), $key);
        }
    }


    private function validateField(string $field, mixed $value, array $rule): ?string
    {
        $type = $rule['type'] ?? 'string';

        // Проверка типа данных
        switch ($type) {
            case 'int':
                if (!filter_var($value, FILTER_VALIDATE_INT) && (int)$value !== $value) {
                    return "Field '$field' must be integer";
                }
                break;

            case 'float':
                if (!filter_var($value, FILTER_VALIDATE_FLOAT && (float)$value !== $value)) {
                    return "Field '$field' must be float";
                }
                break;

            case
            'string':
                if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                    return "Field '$field' exceeds maximum length";
                }
                break;
        }

        // Дополнительные проверки
//        if (isset($rule['min']) && $value < $rule['min']) {
//            return "Field '$field' must be at least {$rule['min']}";
//        }
//
//        if (isset($rule['max']) && $value > $rule['max']) {
//            return "Field '$field' must be at most {$rule['max']}";
//        }

        return null;
    }

    private function validateNumberColumns(array $row): ?string
    {
        return count($row) !== self::ROW_NUMBER ? 'Row does not match number of rows' : null;
    }

    private function prepareData(array $row, int $line): array
    {
        return [
            'line' => $line,
            'data' => $row,
            'errors' => [],
        ];
    }

    /**
     * @throws \Exception
     */
    private function processRow(array $row): array
    {
        $processRow = [
            'code' => $row[0] ?? '',
            'name' => $row[1] ?? '',
            'description' => $row[2] ?? '',
            'stock' => $row[3] ?? '',
            'cost' => $row[4] ?? '',
            'discount' => $row[5] ?? '',
            'errors' => [],
        ];

        $columnValidatedRow = $this->validateNumberColumns($processRow);

        return [
            'line' => $lineNumber,
            'errors' => [],
//            'data' => $this->prepareData($row),
//            'code' => $row[0] ?? '',
//            'name' => $row[1] ?? '',
//            'description' => $row[2] ?? '',
//            'stock' => $row[3] ?? '',
//            'cost' => $row[4] ?? '',
//            'discount' => $row[5] ?? '',
//            'data' => new ProductDTO($row[0], $row[1], $row[2], (int)$row[3], (float)$row[4], (bool)$row[5]),
        ];
    }
}