<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityProcessor;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Modules\Product\ProductPropertyKeys;
use DynamicProperties\Modules\Product\Operations\CalculateTotalPriceOperation;
use DynamicProperties\Modules\Product\Operations\CheckStockAvailabilityOperation;
use DynamicProperties\Modules\Product\Operations\FormatProductInfoOperation;

$laptop = new Entity(101, "Gaming Laptop ASUS ROG");
$laptop
    ->setProperty(ProductPropertyKeys::$PRICE, 1299.99)
    ->setProperty(ProductPropertyKeys::$QUANTITY, 15)
    ->setProperty(ProductPropertyKeys::$CATEGORY, "Electronics")
    ->setProperty(ProductPropertyKeys::$SKU, "LAPTOP-ROG-001")
    ->setProperty(ProductPropertyKeys::$WEIGHT, 2.5);

$keyboard = new Entity(102, "Mechanical Keyboard");
$keyboard
    ->setProperty(ProductPropertyKeys::$PRICE, 89.99)
    ->setProperty(ProductPropertyKeys::$QUANTITY, 0)
    ->setProperty(ProductPropertyKeys::$CATEGORY, "Accessories")
    ->setProperty(ProductPropertyKeys::$SKU, "KB-MECH-042")
    ->setProperty(ProductPropertyKeys::$WEIGHT, 0.8);

$productProcessor = new EntityProcessor();
$productProcessor
    ->registerOperation(new CalculateTotalPriceOperation())
    ->registerOperation(new CheckStockAvailabilityOperation())
    ->registerOperation(new FormatProductInfoOperation());

$laptopInfo = $productProcessor->execute('format_product_info', $laptop);
$totalValue = $productProcessor->execute('calculate_total_price', $laptop);

echo "\n1. Laptop analysis ('{$laptop->getName()}'):\n";
echo "\n{$laptopInfo}\n";
echo "   Total value: \${$totalValue}\n";

$checkContext1 = new PropertyContainer();
$checkContext1->setByName('requested_quantity', 10);
$availability1 = $productProcessor->execute('check_stock_availability', $laptop, $checkContext1);

echo "\n   Checking availability for 10 units: ";
if ($availability1['available']) {
    echo "✓ Available\n";
} else {
    echo "✗ Out of stock (shortage: {$availability1['shortage']} pcs)\n";
}

$checkContext2 = new PropertyContainer();
$checkContext2->setByName('requested_quantity', 20);
$availability2 = $productProcessor->execute('check_stock_availability', $laptop, $checkContext2);

echo "   Checking availability for 20 units: ";
if ($availability2['available']) {
    echo "✓ Available\n";
} else {
    echo "✗ Out of stock (shortage: {$availability2['shortage']} pcs)\n";
}

echo "\n2. Product analysis for '{$keyboard->getName()}' (Out of Stock):\n";

$keyboardInfo = $productProcessor->execute('format_product_info', $keyboard);
echo "\n{$keyboardInfo}\n";

$checkContext3 = new PropertyContainer();
$checkContext3->setByName('requested_quantity', 1);
$availability3 = $productProcessor->execute('check_stock_availability', $keyboard, $checkContext3);

echo "\n   Checking availability for 1 unit: ";
if ($availability3['available']) {
    echo "✓ Available\n";
} else {
    echo "✗ Out of stock (shortage: {$availability3['shortage']} pcs)\n";
}

echo "\n3. Batch analysis for laptop:\n";

$allResults = $productProcessor->executeMultiple(
    ['calculate_total_price', 'format_product_info'],
    $laptop
);

echo "\n   Total price: \${$allResults['calculate_total_price']}\n";
echo "   Product information:\n";
foreach (explode("\n", $allResults['format_product_info']) as $line) {
    echo "   {$line}\n";
}

echo "\n=== Example completed successfully ===\n";
