<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DynamicProperties\Core\Entity;
use DynamicProperties\Core\PropertyKey;

echo "1 Creating property keys...\n";
$emailKey = PropertyKey::create('example.email');
$ageKey = PropertyKey::create('example.age');
$activeKey = PropertyKey::create('example.is_active');
echo "   ✓ Keys created: email, age, is_active\n\n";

echo "2 Creating new Entity...\n";
$myEntity = new Entity(1, "Test Entity");
echo "   ✓ Entity created with ID=1 and name='Test Entity'\n\n";

echo "3 Setting property values...\n";
$myEntity->setProperty($emailKey, "test@example.com");
$myEntity->setProperty($ageKey, 25);
$myEntity->setProperty($activeKey, true);
echo "   ✓ Properties set: email='test@example.com', age=25, is_active=true\n\n";

echo "4 Retrieving stored property values...\n";
$storedEmail = $myEntity->getProperty($emailKey);
$storedAge = $myEntity->getProperty($ageKey);
$storedActive = $myEntity->getProperty($activeKey);
echo "   → Email: {$storedEmail}\n";
echo "   → Age: {$storedAge}\n";
echo "   → Active: " . ($storedActive ? 'true' : 'false') . "\n\n";

echo "5 Accessing a non-existent property (phone)...\n";
$phoneKey = PropertyKey::create('example.phone');
$storedPhone = $myEntity->getProperty($phoneKey, 'Not specified');
echo "   → Phone: {$storedPhone}\n\n";

echo "6 Checking if properties exist...\n";
$hasEmail = $myEntity->hasProperty($emailKey);
$hasPhone = $myEntity->hasProperty($phoneKey);
echo "   → Has email? " . ($hasEmail ? 'Yes' : 'No') . "\n";
echo "   → Has phone? " . ($hasPhone ? 'Yes' : 'No') . "\n\n";

echo "7 Setting and getting custom property by name...\n";
$myEntity->properties()->setByName('custom_field', 'custom value');
$customValue = $myEntity->properties()->getByName('custom_field');
echo "   ✓ Custom field set successfully.\n";
echo "   → Custom field value: {$customValue}\n\n";

echo "=== Demo complete! ===\n";
