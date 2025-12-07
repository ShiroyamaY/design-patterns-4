<?php

declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\Exception\InvalidPropertyValueTypeException;
use DynamicProperties\Core\PropertyKey;

echo " Пример 3: Защита от конфликтов ключей свойств \n\n";

echo "1. Регистрация уникальных типизированных ключей:\n";
$module1EmailKey = PropertyKey::string('module1.email');
echo "   - Модуль 1: зарегистрирован ключ 'module1.email' (string)\n";
$module2EmailKey = PropertyKey::string('module2.email');
echo "   - Модуль 2: зарегистрирован ключ 'module2.email' (string)\n";
$module1AgeKey = PropertyKey::int('module1.age');
echo "   - Модуль 1: зарегистрирован ключ 'module1.age' (int)\n";

echo "\n   Всего зарегистрированных ключей: " . PropertyKey::getRegisteredCount() . "\n";
echo "\n2. Попытка зарегистрировать дублирующий ключ:\n";

try {
    echo "   Модуль 3 пытается зарегистрировать 'module1.email'...\n";
    PropertyKey::string('module1.email');
    echo "   ! ОШИБКА: Дублирующий ключ был зарегистрирован!\n";
} catch (\RuntimeException $exception) {
    echo "   - Конфликт предотвращён!\n";
    echo "   Сообщение: {$exception->getMessage()}\n";
}

echo "\n3. Проверка существования ключей в реестре:\n";

$exists1 = PropertyKey::isRegistered('module1.email');
echo "   'module1.email' зарегистрирован: " . ($exists1 ? 'да' : 'нет') . "\n";
$exists2 = PropertyKey::isRegistered('module2.email');
echo "   'module2.email' зарегистрирован: " . ($exists2 ? 'да' : 'нет') . "\n";
$exists3 = PropertyKey::isRegistered('nonexistent.key');
echo "   'nonexistent.key' зарегистрирован: " . ($exists3 ? 'да' : 'нет') . "\n";

echo "\n4. Список всех зарегистрированных ключей:\n";

$allKeys = PropertyKey::getAllRegisteredKeys();
foreach ($allKeys as $keyName => $keyObject) {
    echo "   - '{$keyName}' (ID: {$keyObject->getId()}, тип: {$keyObject->getType()->describe()})\n";
}

try {
    echo "   Модуль UserModule регистрирует 'email'...\n";
    PropertyKey::string('email');
    echo "   - Успешно зарегистрирован\n\n";

    echo "   Модуль NotificationModule пытается зарегистрировать 'email'...\n";
    PropertyKey::string('email');
} catch (\RuntimeException $exception) {
    echo "   - Конфликт предотвращён!\n";
    echo "   Оба модуля попытались использовать одно и то же имя ключа для РАЗНЫХ данных.\n\n";
}
echo "\n5. демонстрирую тип сефти йоу:\n";


$priceKey = PropertyKey::float('demo.product.price');
$demoProduct = new Entity(42, 'Demo Entity');
$demoProduct->setProperty($priceKey, 199.99);

echo "   - изначальная цена брух: " . $demoProduct->getProperty($priceKey) . "\n";

try {
    $demoProduct->setProperty($priceKey, '199.99');
} catch (InvalidPropertyValueTypeException $exception) {
    echo "   - тригер брух: {$exception->getMessage()}\n";
}
