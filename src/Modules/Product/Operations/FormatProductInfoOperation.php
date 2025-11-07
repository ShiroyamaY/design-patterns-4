<?php

namespace DynamicProperties\Modules\Product\Operations;

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityOperationInterface;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\Product\ProductPropertyKeys;

class FormatProductInfoOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): mixed
    {
        $productPrice = $entity->getProperty(ProductPropertyKeys::$PRICE, 0.0);
        $productQuantity = $entity->getProperty(ProductPropertyKeys::$QUANTITY, 0);
        $productCategory = $entity->getProperty(ProductPropertyKeys::$CATEGORY, 'Uncategorized');
        $productSku = $entity->getProperty(ProductPropertyKeys::$SKU, 'N/A');
        $productWeight = $entity->getProperty(ProductPropertyKeys::$WEIGHT);

        $stockStatus = $productQuantity > 0
            ? "✓ In Stock ({$productQuantity} units)"
            : "✗ Out of Stock";

        $weightInfo = $productWeight !== null
            ? sprintf("Weight: %.2f kg\n  ", $productWeight)
            : "";

        return sprintf(
            "Product #%d: %s\n" .
            "  Category: %s\n" .
            "  SKU: %s\n" .
            "  Price: $%.2f\n" .
            "  %sStock: %s",
            $entity->getId(),
            $entity->getName(),
            $productCategory,
            $productSku,
            $productPrice,
            $weightInfo,
            $stockStatus
        );
    }

    public function getName(): string
    {
        return 'format_product_info';
    }
}