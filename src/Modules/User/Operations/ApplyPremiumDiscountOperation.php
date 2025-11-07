<?php

namespace DynamicProperties\Modules\User\Operations;

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityOperationInterface;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\User\UserPropertyKeys;

class ApplyPremiumDiscountOperation implements EntityOperationInterface
{
    private const float DEFAULT_PREMIUM_DISCOUNT_PERCENT = 15.0;

    public function execute(Entity $entity, PropertyContainer $executionContext): mixed
    {
        $isPremiumUser = $entity->getProperty(UserPropertyKeys::$IS_PREMIUM, false);
        $basePrice = $executionContext->getByName('base_price', 100.0);

        $discountPercent = $executionContext->getByName(
            'premium_discount_percent',
            self::DEFAULT_PREMIUM_DISCOUNT_PERCENT
        );

        if ($isPremiumUser) {
            $discountMultiplier = 1.0 - ($discountPercent / 100.0);
            $finalPrice = $basePrice * $discountMultiplier;
            $amountSaved = $basePrice - $finalPrice;

            return [
                'original_price' => $basePrice,
                'discount_percent' => $discountPercent,
                'final_price' => round($finalPrice, 2),
                'amount_saved' => round($amountSaved, 2),
            ];
        }

        return [
            'original_price' => $basePrice,
            'discount_percent' => 0.0,
            'final_price' => $basePrice,
            'amount_saved' => 0.0,
        ];
    }

    public function getName(): string
    {
        return 'apply_premium_discount';
    }
}