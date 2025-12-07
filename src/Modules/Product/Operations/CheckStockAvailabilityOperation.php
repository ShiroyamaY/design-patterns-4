<?php

namespace DynamicProperties\Modules\Product\Operations;

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityOperationInterface;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\Product\ProductPropertyKeys;

class CheckStockAvailabilityOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): array
    {
        $availableQuantity = $entity->getProperty(ProductPropertyKeys::$QUANTITY, 0);
        $requestedQuantity = $executionContext->getByName('requested_quantity', 1);

        $isAvailable = $availableQuantity >= $requestedQuantity;
        $shortage = max(0, $requestedQuantity - $availableQuantity);

        return [
            'available' => $isAvailable,
            'in_stock' => $availableQuantity,
            'requested' => $requestedQuantity,
            'shortage' => $shortage,
        ];
    }

    public function getName(): string
    {
        return 'check_stock_availability';
    }
}