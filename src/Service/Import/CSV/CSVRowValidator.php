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

        return $this->convertValues($this->combineRow($row));

    }

    private function combineRow(array $row): array
    {
        return ['data' => array_combine(self::PRODUCT_HEADER_LIST, $row), 'errors' => []];
    }

    /**
     * @param array $row
     * @return array['data' => [], 'errors' => []]
     */
    private function convertValues(array $row): array
    {
        // Convert product code
        if (empty($row['data'][self::PRODUCT_CODE_HEADER])) {
            $row['errors'][] = "Field [" . self::PRODUCT_CODE_HEADER . "] empty or null";
        } else {
            $row['data'][self::PRODUCT_CODE_HEADER] = trim((string)$row['data'][self::PRODUCT_CODE_HEADER]);
        }

        // Convert Product name
        $row['data'][self::PRODUCT_NAME_HEADER] = trim((string)$row['data'][self::PRODUCT_NAME_HEADER]);

        // Convert Product description
        $row['data'][self::PRODUCT_DESCRIPTION_HEADER] = trim((string)$row['data'][self::PRODUCT_DESCRIPTION_HEADER]);

        // Convert Product quantity
        if (!is_numeric($row['data'][self::PRODUCT_QUANTITY_HEADER])) {
            $row['errors'][] = "Field [" . self::PRODUCT_QUANTITY_HEADER . "] is not a number";
        } else {
            $row['data'][self::PRODUCT_QUANTITY_HEADER] = (int)$row['data'][self::PRODUCT_QUANTITY_HEADER];
        }

        // Convert Product price
        if (!is_numeric($row['data'][self::PRODUCT_PRICE_HEADER])) {
            $row['errors'][] = "Field [" . self::PRODUCT_PRICE_HEADER . "] is not a number";
        } else {
            $row['data'][self::PRODUCT_PRICE_HEADER] = (float)$row['data'][self::PRODUCT_PRICE_HEADER];
        }

        // Convert Product Discounted
        $row['data'][self::PRODUCT_DISCONTINUED_HEADER] = $row['data'][self::PRODUCT_DISCONTINUED_HEADER] == 'yes' ? 1 : 0;

        return $row;
    }
}