<?php
namespace DynamicProperties\Core;

class PropertyContainer
{
    /**
     * @var array<int, mixed>
     */
    private array $storageByIntegerId = [];

    /**
     * @var array<string, mixed>
     */
    private array $storageByStringKey = [];

    public function set(PropertyKey $propertyKey, mixed $value): self
    {
        $this->storageByIntegerId[$propertyKey->getId()] = $value;
        return $this;
    }

    public function get(PropertyKey $propertyKey, mixed $defaultValue = null): mixed
    {
        return $this->storageByIntegerId[$propertyKey->getId()] ?? $defaultValue;
    }

    public function has(PropertyKey $propertyKey): bool
    {
        return isset($this->storageByIntegerId[$propertyKey->getId()]);
    }

    public function remove(PropertyKey $propertyKey): self
    {
        unset($this->storageByIntegerId[$propertyKey->getId()]);
        return $this;
    }

    public function setByName(string $stringKey, mixed $value): self
    {
        $this->storageByStringKey[$stringKey] = $value;
        return $this;
    }

    public function getByName(string $stringKey, mixed $defaultValue = null): mixed
    {
        return $this->storageByStringKey[$stringKey] ?? $defaultValue;
    }

    public function hasByName(string $stringKey): bool
    {
        return isset($this->storageByStringKey[$stringKey]);
    }

    public function removeByName(string $stringKey): self
    {
        unset($this->storageByStringKey[$stringKey]);
        return $this;
    }

    public function getAllProperties(): array
    {
        return [
            'byPropertyKey' => $this->storageByIntegerId,
            'byStringKey' => $this->storageByStringKey,
        ];
    }

    public function clear(): void
    {
        $this->storageByIntegerId = [];
        $this->storageByStringKey = [];
    }

    public function count(): int
    {
        return count($this->storageByIntegerId) + count($this->storageByStringKey);
    }
}