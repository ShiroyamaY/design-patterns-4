<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\PropertyKey;

echo "1 Создание типизированных ключей свойств...\n";
$emailKey = PropertyKey::string('example.email');
$ageKey = PropertyKey::int('example.age');
$activeKey = PropertyKey::bool('example.is_active');
echo "   - Ключи созданы: email (string), age (int), is_active (bool)\n\n";

echo "2 Создание нового Entity...\n";
$myEntity = new Entity(1, "Test Entity");
echo "   - Entity создан с ID=1 и именем 'Test Entity'\n\n";

echo "3 Установка значений свойств...\n";
$myEntity->setProperty($emailKey, "test@example.com");
$myEntity->setProperty($ageKey, 25);
$myEntity->setProperty($activeKey, true);
echo "   - Свойства установлены: email='test@example.com', age=25, is_active=true\n\n";

echo "4 Получение сохранённых значений свойств...\n";
$storedEmail = $myEntity->getProperty($emailKey);
$storedAge = $myEntity->getProperty($ageKey);
$storedActive = $myEntity->getProperty($activeKey);
echo "   - Email: {$storedEmail}\n";
echo "   - Возраст: {$storedAge}\n";
echo "   - Активен: " . ($storedActive ? 'true' : 'false') . "\n\n";

echo "5 Доступ к несуществующему свойству (phone)...\n";
$phoneKey = PropertyKey::string('example.phone');
$storedPhone = $myEntity->getProperty($phoneKey, 'Не указано');
echo "   - Телефон: {$storedPhone}\n\n";

echo "6 Проверка существования свойств...\n";
$hasEmail = $myEntity->hasProperty($emailKey);
$hasPhone = $myEntity->hasProperty($phoneKey);
echo "   - Есть email? " . ($hasEmail ? 'Да' : 'Нет') . "\n";
echo "   - Есть телефон? " . ($hasPhone ? 'Да' : 'Нет') . "\n\n";

echo "7 Установка и получение пользовательского свойства по имени...\n";
$myEntity->properties()->setByName('custom_field', 'custom value');
$customValue = $myEntity->properties()->getByName('custom_field');
