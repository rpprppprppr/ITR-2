<?php

class OrderItem {
    public AbstractProduct $product;
    public int $quantity;
    public float $price;

    public function __construct(AbstractProduct $product, int $quantity, float $price) {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function getTotalPrice(): float {
        return $this->price * $this->quantity;
    }
}