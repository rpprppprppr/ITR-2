<?php

require_once 'AbstractProduct.php';

class WeightProduct extends AbstractProduct {
    public float $weightInStock;

    public function __construct(int $id, string $name, float $price, string $description, float $weightInStock = 0) {
        parent::__construct($id, $name, $price, $description, 'weight');
        $this->weightInStock = $weightInStock;
    }

    public function calculateFinalCost(float $quantity = 1): float {
        $quantity = min($quantity, $this->weightInStock);
        return $this->price * $quantity;
    }

    public function updateStock(float $weight): void {
        $this->weightInStock += $weight;
    }

    public function isAvailable(): bool {
        return $this->weightInStock > 0;
    }
}