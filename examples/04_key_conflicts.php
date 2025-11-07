<?php
/**
 * Example 4: Demonstration of Property Key Conflict Protection
 *
 * Shows how the system prevents naming conflicts between modules
 * through a centralized PropertyKey registry.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DynamicProperties\Core\PropertyKey;

echo "=== Example 4: Property Key Conflict Protection ===\n\n";

echo "1. Registering unique keys:\n";

$module1EmailKey = PropertyKey::create('module1.email');
echo "   ✓ Module 1: registered key 'module1.email'\n";
$module2EmailKey = PropertyKey::create('module2.email');
echo "   ✓ Module 2: registered key 'module2.email'\n";
$module1AgeKey = PropertyKey::create('module1.age');
echo "   ✓ Module 1: registered key 'module1.age'\n";

echo "\n   Total registered keys: " . PropertyKey::getRegisteredCount() . "\n";
echo "\n2. Attempting to register a duplicate key:\n";

try {
    echo "   Module 3 tries to register 'module1.email'...\n";
    $duplicateKey = PropertyKey::create('module1.email');

    echo "   ✗ ERROR: Duplicate key was registered!\n";

} catch (\RuntimeException $exception) {
    echo "   ✓ Conflict prevented!\n";
    echo "   Message: {$exception->getMessage()}\n";
}

echo "\n3. Checking key existence in the registry:\n";

$exists1 = PropertyKey::isRegistered('module1.email');
echo "   'module1.email' registered: " . ($exists1 ? '✓ Yes' : '✗ No') . "\n";
$exists2 = PropertyKey::isRegistered('module2.email');
echo "   'module2.email' registered: " . ($exists2 ? '✓ Yes' : '✗ No') . "\n";
$exists3 = PropertyKey::isRegistered('nonexistent.key');
echo "   'nonexistent.key' registered: " . ($exists3 ? '✗ Yes' : '✓ No') . "\n";

echo "\n4. Listing all registered keys:\n";

$allKeys = PropertyKey::getAllRegisteredKeys();
foreach ($allKeys as $keyName => $keyObject) {
    echo "   - '{$keyName}' (ID: {$keyObject->getId()})\n";
}

echo "\n5. Why module prefixes are important:\n\n";

echo "  Bad (risk of conflicts):\n";
echo "      Module1: PropertyKey::create('email')\n";
echo "      Module2: PropertyKey::create('email') // CONFLICT!\n\n";

echo "   ✓ Good (unique names):\n";
echo "      Module1: PropertyKey::create('user.email')\n";
echo "      Module2: PropertyKey::create('product.email') // OK\n\n";

echo "6. Real-world conflict scenario:\n\n";

echo "   Imagine two modules:\n";
echo "   - UserModule: wants to store user email\n";
echo "   - NotificationModule: wants to store notification email\n\n";

try {
    echo "   UserModule registers 'email'...\n";
    $userEmail = PropertyKey::create('email');
    echo "   ✓ Successfully registered\n\n";

    echo "   NotificationModule tries to register 'email'...\n";
    $notificationEmail = PropertyKey::create('email');
    echo "   ✗ This should not be executed!\n";

} catch (\RuntimeException $exception) {
    echo "   ✓ Conflict prevented!\n";
    echo "   Both modules tried to use the same key name for DIFFERENT data.\n\n";

    echo "   Correct solution:\n";
    echo "   - UserModule: 'user.email'\n";
    echo "   - NotificationModule: 'notification.email'\n";
}

echo "\n=== Example completed ===\n";
echo "\nConclusion: The centralized registry protects against accidental naming conflicts between modules.\n";
