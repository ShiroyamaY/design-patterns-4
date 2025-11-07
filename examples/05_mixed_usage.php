<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\EntityProcessor;
use DynamicProperties\Core\PropertyContainer;
use DynamicProperties\Core\EntityOperationInterface;
use DynamicProperties\Modules\User\UserPropertyKeys;
use DynamicProperties\Modules\Product\ProductPropertyKeys;

echo "=== Пример 5: Смешанное использование ===\n\n";

echo "1. Создание гибридной сущности (Order):\n";

$order = new Entity(1001, "Order #1001");

$order->setProperty(UserPropertyKeys::$EMAIL, "customer@example.com");
$order->setProperty(UserPropertyKeys::$IS_PREMIUM, true);
echo "   ✓ Добавлены данные покупателя\n";

$order->setProperty(ProductPropertyKeys::$PRICE, 599.99);
$order->setProperty(ProductPropertyKeys::$QUANTITY, 2);
$order->setProperty(ProductPropertyKeys::$CATEGORY, "Electronics");
echo "   ✓ Добавлены данные товара\n";

$order->properties()->setByName('order_date', date('Y-m-d H:i:s'));
$order->properties()->setByName('shipping_address', '123 Main St, City, Country');
$order->properties()->setByName('payment_method', 'Credit Card');
echo "   ✓ Добавлены метаданные заказа\n";

class CalculateOrderTotalOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): mixed
    {
        $itemPrice = $entity->getProperty(ProductPropertyKeys::$PRICE, 0.0);
        $itemQuantity = $entity->getProperty(ProductPropertyKeys::$QUANTITY, 0);
        $isPremium = $entity->getProperty(UserPropertyKeys::$IS_PREMIUM, false);

        $subtotal = $itemPrice * $itemQuantity;

        if ($isPremium) {
            $discount = $subtotal * 0.10;
            $finalTotal = $subtotal - $discount;

            return [
                'subtotal' => round($subtotal, 2),
                'discount' => round($discount, 2),
                'total' => round($finalTotal, 2),
                'premium_applied' => true
            ];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => 0.0,
            'total' => round($subtotal, 2),
            'premium_applied' => false
        ];
    }

    public function getName(): string
    {
        return 'calculate_order_total';
    }
}

class FormatOrderSummaryOperation implements EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): mixed
    {
        $customerEmail = $entity->getProperty(UserPropertyKeys::$EMAIL, 'N/A');
        $isPremium = $entity->getProperty(UserPropertyKeys::$IS_PREMIUM, false);

        $productPrice = $entity->getProperty(ProductPropertyKeys::$PRICE, 0.0);
        $productQuantity = $entity->getProperty(ProductPropertyKeys::$QUANTITY, 0);
        $productCategory = $entity->getProperty(ProductPropertyKeys::$CATEGORY, 'N/A');

        $orderDate = $entity->properties()->getByName('order_date', 'N/A');
        $shippingAddress = $entity->properties()->getByName('shipping_address', 'N/A');
        $paymentMethod = $entity->properties()->getByName('payment_method', 'N/A');

        $premiumBadge = $isPremium ? ' ⭐ [PREMIUM]' : '';

        return sprintf(
            "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
            "Order: %s\n" .
            "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
            "Customer: %s%s\n" .
            "Date: %s\n" .
            "Payment: %s\n\n" .
            "Items:\n" .
            "  %d x %s @ $%.2f each\n" .
            "  Category: %s\n\n" .
            "Shipping Address:\n" .
            "  %s\n" .
            "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━",
            $entity->getName(),
            $customerEmail,
            $premiumBadge,
            $orderDate,
            $paymentMethod,
            $productQuantity,
            $entity->getName(),
            $productPrice,
            $productCategory,
            $shippingAddress
        );
    }

    public function getName(): string
    {
        return 'format_order_summary';
    }
}

echo "\n2. Регистрация кастомных операций:\n";

$orderProcessor = new EntityProcessor();
$orderProcessor
    ->registerOperation(new CalculateOrderTotalOperation())
    ->registerOperation(new FormatOrderSummaryOperation());

echo "   ✓ Зарегистрировано операций: {$orderProcessor->getOperationCount()}\n";

echo "\n3. Обработка заказа:\n\n";

$orderSummary = $orderProcessor->execute('format_order_summary', $order);
echo $orderSummary . "\n\n";

$orderTotal = $orderProcessor->execute('calculate_order_total', $order);

echo "Финансы:\n";
echo "  Subtotal: \${$orderTotal['subtotal']}\n";
if ($orderTotal['premium_applied']) {
    echo "  Premium Discount (10%): -\${$orderTotal['discount']}\n";
}
echo "  ────────────────────\n";
echo "  Total: \${$orderTotal['total']}\n";

echo "\n4. Преимущества смешанного подхода:\n\n";

echo "   ✓ Типизированные ключи (UserPropertyKeys, ProductPropertyKeys):\n";
echo "     - IDE знает тип данных\n";
echo "     - Защита от конфликтов имён\n";
echo "     - Переиспользуются между модулями\n\n";

echo "   ✓ Строковые ключи (order_date, shipping_address):\n";
echo "     - Быстро добавить уникальные данные\n";
echo "     - Подходит для одноразовых полей\n";
echo "     - Не требует регистрации PropertyKey\n\n";

echo "   ✓ Кастомные операции:\n";
echo "     - Реализуют специфичную бизнес-логику\n";
echo "     - Используют данные из разных модулей\n";
echo "     - Легко тестируются изолированно\n";

echo "\n=== Пример завершён ===\n";