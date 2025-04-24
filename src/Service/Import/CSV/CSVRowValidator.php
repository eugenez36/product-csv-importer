<?php

namespace App\Service\Import\CSV;

class CSVRowValidator extends CSVConstants
{
    public function validateRow(array $headers, array $row): array
    {
        $processedRow = [
            'data' => $row,
            'errors' => [],
        ];

        if (count($headers) !== count($row)) {
            $processedRow['errors'][] = 'Row has incorrect number of columns';
            return $processedRow;
        }

        return $this->convertValues($processedRow);

    }

    /**
     * @param array $row
     * @return array['data' => [], 'errors' => []]
     */
    private function convertValues(array $row): array
    {
        // Convert product code
        if (empty($row['data'][0])) {
            $row['errors'][] = "Field [" . self::PRODUCT_CODE_HEADER . "] empty or null";
        } else {
            $row['data'][0] = trim((string)$row['data'][0]);
        }

        // Convert Product name
        $row['data'][1] = trim((string)$row['data'][1]);

        // Convert Product description
        $row['data'][2] = trim((string)$row['data'][2]);

        // Convert Product quantity
        if (!is_numeric($row['data'][3])) {
            $row['errors'][] = "Field [" . self::PRODUCT_QUANTITY_HEADER . "] is not a number";
        } else {
            $row['data'][3] = (int)$row['data'][3];
        }

        // Convert Product price
        if (!is_numeric($row['data'][4])) {
            $row['errors'][] = "Field [" . self::PRODUCT_PRICE_HEADER . "] is not a number";
        } else {
            $row['data'][4] = (float)$row['data'][4];
        }

        // Convert Product Discounted
        $row['data'][5] = $row['data'][5] == 'yes' ? 1 : 0;

        return $row;
    }
}