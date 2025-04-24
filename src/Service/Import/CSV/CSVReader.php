<?php

namespace App\Service\Import\CSV;

use Generator;

class CSVReader
{
    public function readFile(string $filePath): Generator
    {
        $handle = fopen($filePath, 'r');

        //Read and skip headers
        fgetcsv($handle);

        $line = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            yield $line => $row;
        }

        fclose($handle);
    }
}