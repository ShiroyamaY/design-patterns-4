<?php

namespace DynamicProperties\Modules\User\Operations;

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityOperationInterface;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\User\UserPropertyKeys;

class FormatUserInfoOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): mixed
    {
        $userEmail = $entity->getProperty(UserPropertyKeys::$EMAIL);
        $userAge = $entity->getProperty(UserPropertyKeys::$AGE);
        $userPhone = $entity->getProperty(UserPropertyKeys::$PHONE);
        $isPremiumUser = $entity->getProperty(UserPropertyKeys::$IS_PREMIUM, false);

        $premiumBadge = $isPremiumUser ? ' [PREMIUM]' : '';

        $formattedInfo = sprintf(
            "User #%d: %s%s\n" .
            "  Email: %s\n" .
            "  Age: %s\n" .
            "  Phone: %s",
            $entity->getId(),
            $entity->getName(),
            $premiumBadge,
            $userEmail ?? 'not-specified',
            $userAge !== null ? (string)$userAge : 'unknown',
            $userPhone ?? 'not-specified'
        );

        return $formattedInfo;
    }

    public function getName(): string
    {
        return 'format_user_info';
    }
}
