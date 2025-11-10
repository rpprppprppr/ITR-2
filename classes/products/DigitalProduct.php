<?php

require_once 'AbstractProduct.php';

class DigitalProduct extends AbstractProduct {

    public function __construct(int $id, string $name, float $price, string $description) {
        parent::__construct($id, $name, $price, $description, 'digital');
    }

    public function calculateFinalCost(float $quantity = 1): float {
        return $this->price / 2 * $quantity;
    }
}