<?php

namespace DynamicProperties\Modules\Product;

use DynamicProperties\Core\PropertyKey;

class ProductPropertyKeys
{
    public static PropertyKey $PRICE;

    public static PropertyKey $QUANTITY;

    public static PropertyKey $CATEGORY;

    public static PropertyKey $SKU;

    public static PropertyKey $WEIGHT;

    public static function initialize(): void
    {
        self::$PRICE = PropertyKey::float('product.price');
        self::$QUANTITY = PropertyKey::int('product.quantity');
        self::$CATEGORY = PropertyKey::string('product.category');
        self::$SKU = PropertyKey::string('product.sku');
        self::$WEIGHT = PropertyKey::float('product.weight', true);
    }
}

ProductPropertyKeys::initialize();
