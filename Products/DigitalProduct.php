<?php

require_once 'Product.php';

class DigitalProduct extends Product
{
    public function calculateCost(float $quantity): float
    {
        return ($this->basePrice / 2) * $quantity;
    }
}