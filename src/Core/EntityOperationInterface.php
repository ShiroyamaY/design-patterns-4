<?php
namespace DynamicProperties\Core;

interface EntityOperationInterface
{
    public function execute(Entity $entity, PropertyContainer $executionContext): mixed;
    public function getName(): string;
}