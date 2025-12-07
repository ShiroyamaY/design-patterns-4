<?php

namespace DynamicProperties\Core;

use DynamicProperties\Core\Exception\InvalidPropertyValueTypeException;
use RuntimeException;

/**
 * @template TValue of mixed
 */
class PropertyKey
{
    private static int $nextAvailableId = 1;

    /**
     * @var array<string, PropertyKey>
     */
    private static array $globalRegistry = [];

    private int $uniqueId;

    private string $keyName;

    /**
     * @param PropertyType<TValue> $propertyType
     */
    private function __construct(string $keyName, private readonly PropertyType $propertyType)
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

    /**
     * @template TValue
     * @param PropertyType<TValue> $propertyType
     * @return self<TValue>
     */
    public static function create(string $keyName, PropertyType $propertyType): self
    {
        return new self($keyName, $propertyType);
    }

    /**
     * @return self<string>
     */
    public static function string(string $keyName, bool $nullable = false): self
    {
        return self::create($keyName, PropertyType::string($nullable));
    }

    /**
     * @return self<int>
     */
    public static function int(string $keyName, bool $nullable = false): self
    {
        return self::create($keyName, PropertyType::int($nullable));
    }

    /**
     * @return self<float>
     */
    public static function float(string $keyName, bool $nullable = false): self
    {
        return self::create($keyName, PropertyType::float($nullable));
    }

    /**
     * @return self<bool>
     */
    public static function bool(string $keyName, bool $nullable = false): self
    {
        return self::create($keyName, PropertyType::bool($nullable));
    }

    /**
     * @return self<array>
     */
    public static function array(string $keyName, bool $nullable = false): self
    {
        return self::create($keyName, PropertyType::array($nullable));
    }

    /**
     * @template TObject of object
     * @param class-string<TObject> $classOrInterface
     * @return self<TObject>
     */
    public static function object(string $keyName, string $classOrInterface, bool $nullable = false): self
    {
        return self::create($keyName, PropertyType::object($classOrInterface, $nullable));
    }

    /**
     * @return self<mixed>
     */
    public static function mixed(string $keyName): self
    {
        return self::create($keyName, PropertyType::mixed());
    }

    public function getId(): int
    {
        return $this->uniqueId;
    }

    public function getName(): string
    {
        return $this->keyName;
    }

    /**
     * @return PropertyType<TValue>
     */
    public function getType(): PropertyType
    {
        return $this->propertyType;
    }

    /**
     * @param mixed $value
     */
    public function assertValueType(mixed $value): void
    {
        if ($this->propertyType->accepts($value)) {
            return;
        }

        $providedType = get_debug_type($value);
        throw new InvalidPropertyValueTypeException(
            "Invalid value for property '{$this->keyName}'. Expected {$this->propertyType->describe()}, got {$providedType}."
        );
    }

    public function accepts(mixed $value): bool
    {
        return $this->propertyType->accepts($value);
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
