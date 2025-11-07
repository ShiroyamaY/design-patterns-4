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
        self::$PRICE = PropertyKey::create('product.price');
        self::$QUANTITY = PropertyKey::create('product.quantity');
        self::$CATEGORY = PropertyKey::create('product.category');
        self::$SKU = PropertyKey::create('product.sku');
        self::$WEIGHT = PropertyKey::create('product.weight');
    }
}

ProductPropertyKeys::initialize();