<?php

namespace DynamicProperties\Core;

class EntityProcessor
{
    private array $registeredOperations = [];

    public function registerOperation(EntityOperationInterface $operation): self
    {
        $operationName = $operation->getName();

        if (isset($this->registeredOperations[$operationName])) {
            throw new \RuntimeException(
                "Operation '{$operationName}' is already registered. " .
                "Each operation must have a unique name."
            );
        }

        $this->registeredOperations[$operationName] = $operation;
        return $this;
    }

    public function execute(
        string $operationName,
        Entity $targetEntity,
        ?PropertyContainer $executionContext = null
    ): mixed {
        if (!isset($this->registeredOperations[$operationName])) {
            throw new \RuntimeException(
                "Operation '{$operationName}' is not registered. " .
                "Available operations: " . implode(', ', array_keys($this->registeredOperations))
            );
        }

        $executionContext = $executionContext ?? new PropertyContainer();

        return $this->registeredOperations[$operationName]
            ->execute($targetEntity, $executionContext);
    }

    public function executeMultiple(
        array $operationNames,
        Entity $targetEntity,
        ?PropertyContainer $executionContext = null
    ): array {
        $executionContext = $executionContext ?? new PropertyContainer();
        $operationResults = [];

        foreach ($operationNames as $operationName) {
            $operationResults[$operationName] = $this->execute(
                $operationName,
                $targetEntity,
                $executionContext
            );
        }

        return $operationResults;
    }

    public function hasOperation(string $operationName): bool
    {
        return isset($this->registeredOperations[$operationName]);
    }

    public function getAllOperations(): array
    {
        return $this->registeredOperations;
    }

    public function getOperationNames(): array
    {
        return array_keys($this->registeredOperations);
    }

    public function getOperationCount(): int
    {
        return count($this->registeredOperations);
    }
}