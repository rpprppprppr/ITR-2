<?php

require_once 'AbstractProduct.php';

class PhysicalProduct extends AbstractProduct {
    public int $stock;

    public function __construct(int $id, string $name, float $price, string $description, int $stock = 0) {
        parent::__construct($id, $name, $price, $description, 'physical');
        $this->stock = $stock;
    }

    public function calculateFinalCost(float $quantity = 1): float {
        $quantity = min($quantity, $this->stock);
        return $this->price * $quantity;
    }

    public function updateStock(int $quantity): void {
        $this->stock += $quantity;
    }

    public function isAvailable(): bool {
        return $this->stock > 0;
    }
}