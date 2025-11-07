<?php

namespace DynamicProperties\Core;

use RuntimeException;

class PropertyKey
{
    private static int $nextAvailableId = 1;

    /**
     * @var array<string, PropertyKey>
     */
    private static array $globalRegistry = [];

    private int $uniqueId;

    private string $keyName;

    private function __construct(string $keyName)
    {
        if (isset(self::$globalRegistry[$keyName])) {
            throw new RuntimeException(
                "Property key '{$keyName}' is already registered. " .
                "This prevents conflicts between different modules. " .
                "Each module must use unique key names (e.g., 'user.email', 'product.price')."
            );
        }

        $this->uniqueId = self::$nextAvailableId++;
        $this->keyName = $keyName;
        self::$globalRegistry[$keyName] = $this;
    }

    public static function create(string $keyName): self
    {
        return new self($keyName);
    }

    public function getId(): int
    {
        return $this->uniqueId;
    }

    public function getName(): string
    {
        return $this->keyName;
    }

    public static function isRegistered(string $keyName): bool
    {
        return isset(self::$globalRegistry[$keyName]);
    }

    public static function getAllRegisteredKeys(): array
    {
        return self::$globalRegistry;
    }

    public static function getRegisteredCount(): int
    {
        return count(self::$globalRegistry);
    }
}