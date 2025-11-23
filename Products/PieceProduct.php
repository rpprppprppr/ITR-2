<?php

require_once 'Product.php';

class PieceProduct extends Product
{
    public function calculateCost(float $quantity): float
    {
        return $this->basePrice * $quantity;
    }
}