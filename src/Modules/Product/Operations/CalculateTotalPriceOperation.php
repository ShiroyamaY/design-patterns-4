<?php

namespace DynamicProperties\Modules\Product\Operations;

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityOperationInterface;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\Product\ProductPropertyKeys;

class CalculateTotalPriceOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): float
    {
        $productPrice = $entity->getProperty(ProductPropertyKeys::$PRICE, 0.0);
        $productQuantity = $entity->getProperty(ProductPropertyKeys::$QUANTITY, 0);

        $totalValue = $productPrice * $productQuantity;

        return round($totalValue, 2);
    }

    /**
     * @return string Имя операции
     */
    public function getName(): string
    {
        return 'calculate_total_price';
    }
}