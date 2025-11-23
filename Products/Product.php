<?php

abstract class Product
{
    public function __construct(
        public readonly string $name,
        public readonly float $basePrice
    ) {}

    abstract public function calculateCost(float $quantity): float;
}