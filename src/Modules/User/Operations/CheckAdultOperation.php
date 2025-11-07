<?php

namespace DynamicProperties\Modules\User\Operations;

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityOperationInterface;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\User\UserPropertyKeys;

class CheckAdultOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): mixed
    {
        $userAge = $entity->getProperty(UserPropertyKeys::$AGE);

        if ($userAge === null) {
            return false;
        }

        $adultAgeThreshold = $executionContext->getByName('adult_age_threshold', 18);

        return $userAge >= $adultAgeThreshold;
    }

    public function getName(): string
    {
        return 'check_adult';
    }
}