<?php

namespace DynamicProperties\Core;

use Closure;
use InvalidArgumentException;

/**
 * @template TValue of mixed
 */
final readonly class PropertyType
{
    /**
     * @param Closure(mixed):bool $guard
     */
    private function __construct(
        private string  $description,
        private Closure $guard,
        private bool    $allowsNull = false
    ) {
    }

    /**
     * @return self<string>
     */
    public static function string(bool $nullable = false): self
    {
        return new self('string', static fn(mixed $value): bool => is_string($value), $nullable);
    }

    /**
     * @return self<int>
     */
    public static function int(bool $nullable = false): self
    {
        return new self('int', static fn(mixed $value): bool => is_int($value), $nullable);
    }

    /**
     * @return self<float>
     */
    public static function float(bool $nullable = false): self
    {
        return new self('float', static fn(mixed $value): bool => is_float($value), $nullable);
    }

    /**
     * @return self<bool>
     */
    public static function bool(bool $nullable = false): self
    {
        return new self('bool', static fn(mixed $value): bool => is_bool($value), $nullable);
    }

    /**
     * @return self<array>
     */
    public static function array(bool $nullable = false): self
    {
        return new self('array', static fn(mixed $value): bool => is_array($value), $nullable);
    }

    /**
     * @template TObject of object
     * @param class-string<TObject> $classOrInterface
     * @return self<TObject>
     */
    public static function object(string $classOrInterface, bool $nullable = false): self
    {
        if (!class_exists($classOrInterface) && !interface_exists($classOrInterface)) {
            throw new InvalidArgumentException(
                "Class or interface '{$classOrInterface}' must exist to create an object property type."
            );
        }

        return new self(
            $classOrInterface,
            static fn(mixed $value): bool => $value instanceof $classOrInterface,
            $nullable
        );
    }

    /**
     * @return self<mixed>
     */
    public static function mixed(): self
    {
        return new self('mixed', static fn(mixed $value): bool => true);
    }

    /**
     * @template TCustom of mixed
     * @param string $description
     * @param Closure(mixed):bool $guard
     * @return self<TCustom>
     */
    public static function custom(string $description, Closure $guard, bool $nullable = false): self
    {
        return new self($description, $guard, $nullable);
    }

    public function describe(): string
    {
        return $this->allowsNull ? "{$this->description}|null" : $this->description;
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function accepts(mixed $value): bool
    {
        if ($value === null) {
            return $this->allowsNull;
        }

        return ($this->guard)($value);
    }
}
