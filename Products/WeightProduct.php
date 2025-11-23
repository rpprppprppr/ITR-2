<?php

require_once 'Product.php';

class WeightProduct extends Product
{
    public function calculateCost(float $quantity): float
    {
        return $this->basePrice * $quantity;
    }
}