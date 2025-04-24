<?php

namespace App\Service\Import\CSV;

class CSVConstants
{
    public const PRODUCT_CODE_HEADER = 'Product Code';
    public const PRODUCT_NAME_HEADER = 'Product Name';
    public const PRODUCT_DESCRIPTION_HEADER = 'Product Description';
    public const PRODUCT_QUANTITY_HEADER = 'Stock';
    public const PRODUCT_PRICE_HEADER = 'Cost in GBP';
    public const PRODUCT_DISCONTINUED_HEADER = 'Discontinued';

    public const PRODUCT_HEADER_LIST = [
        self::PRODUCT_CODE_HEADER,
        self::PRODUCT_NAME_HEADER,
        self::PRODUCT_DESCRIPTION_HEADER,
        self::PRODUCT_QUANTITY_HEADER,
        self::PRODUCT_PRICE_HEADER,
        self::PRODUCT_DISCONTINUED_HEADER,
    ];
}