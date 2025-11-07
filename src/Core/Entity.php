<?php
namespace DynamicProperties\Core;

class Entity
{
    private int $entityId;

    private string $entityName;

    private PropertyContainer $dynamicProperties;

    public function __construct(int $entityId, string $entityName)
    {
        $this->entityId = $entityId;
        $this->entityName = $entityName;
        $this->dynamicProperties = new PropertyContainer();
    }

    public function getId(): int
    {
        return $this->entityId;
    }

    public function getName(): string
    {
        return $this->entityName;
    }

    public function properties(): PropertyContainer
    {
        return $this->dynamicProperties;
    }

    public function setProperty(PropertyKey $propertyKey, mixed $value): self
    {
        $this->dynamicProperties->set($propertyKey, $value);
        return $this;
    }

    public function getProperty(PropertyKey $propertyKey, mixed $defaultValue = null): mixed
    {
        return $this->dynamicProperties->get($propertyKey, $defaultValue);
    }

    public function hasProperty(PropertyKey $propertyKey): bool
    {
        return $this->dynamicProperties->has($propertyKey);
    }

    public function removeProperty(PropertyKey $propertyKey): self
    {
        $this->dynamicProperties->remove($propertyKey);
        return $this;
    }
}