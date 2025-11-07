<?php
require_once __DIR__ . '/../vendor/autoload.php';

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityProcessor;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\User\UserPropertyKeys;
use DynamicProperties\Modules\User\Operations\CheckAdultOperation;
use DynamicProperties\Modules\User\Operations\FormatUserInfoOperation;
use DynamicProperties\Modules\User\Operations\ApplyPremiumDiscountOperation;

$regularUser = new Entity(1, "Ivan Petrov");
$regularUser
    ->setProperty(UserPropertyKeys::$EMAIL, "ivan@example.com")
    ->setProperty(UserPropertyKeys::$AGE, 25)
    ->setProperty(UserPropertyKeys::$PHONE, "+7-900-123-4567")
    ->setProperty(UserPropertyKeys::$IS_PREMIUM, false);

$premiumUser = new Entity(2, "Maria Ivanova");
$premiumUser
    ->setProperty(UserPropertyKeys::$EMAIL, "maria@example.com")
    ->setProperty(UserPropertyKeys::$AGE, 17)
    ->setProperty(UserPropertyKeys::$PHONE, "+7-900-987-6543")
    ->setProperty(UserPropertyKeys::$IS_PREMIUM, true);

$userProcessor = new EntityProcessor();
$userProcessor
    ->registerOperation(new CheckAdultOperation())
    ->registerOperation(new FormatUserInfoOperation())
    ->registerOperation(new ApplyPremiumDiscountOperation());

$priceContext = new PropertyContainer();
$priceContext->setByName('base_price', 1000.0);

echo "Regular User (Ivan Petrov)\n";
$isAdult = $userProcessor->execute('check_adult', $regularUser);
echo "Adult check: " . ($isAdult ? "Yes" : "No") . "\n";

$userInfo = $userProcessor->execute('format_user_info', $regularUser);
echo "Formatted info: " . json_encode($userInfo, JSON_PRETTY_PRINT) . "\n";

$discountInfo = $userProcessor->execute('apply_premium_discount', $regularUser, $priceContext);
echo "Discount info: " . json_encode($discountInfo, JSON_PRETTY_PRINT) . "\n\n";

echo "Premium User (Maria Ivanova)\n";
$isAdult = $userProcessor->execute('check_adult', $premiumUser);
echo "Adult check: " . ($isAdult ? "Yes" : "No") . "\n";

$userInfo = $userProcessor->execute('format_user_info', $premiumUser);
echo "Formatted info: " . json_encode($userInfo, JSON_PRETTY_PRINT) . "\n";

$discountInfo = $userProcessor->execute('apply_premium_discount', $premiumUser, $priceContext);
echo "Discount info: " . json_encode($discountInfo, JSON_PRETTY_PRINT) . "\n\n";

echo "Multiple Operations (Regular User)\n";
$allResults = $userProcessor->executeMultiple(
    ['check_adult', 'format_user_info'],
    $regularUser
);
echo "Results: " . json_encode($allResults, JSON_PRETTY_PRINT) . "\n";
