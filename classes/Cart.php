<?php

require_once 'OrderItem.php';

class Cart {
    public array $items = [];

    public function addProduct(AbstractProduct $product, int $quantity): void {
        foreach ($this->items as $item) {
            if ($item->product->id === $product->id) {
                $item->quantity = $quantity;
                return;
            }
        }
        $this->items[] = new OrderItem($product, $quantity, $product->price);
    }

    public function removeProduct(int $product_id): void {
        $this->items = array_filter($this->items, fn($item) => $item->product->id !== $product_id);
    }

    public function updateQuantity(int $product_id, int $quantity): void {
        foreach ($this->items as $item) {
            if ($item->product->id === $product_id) {
                $item->quantity = $quantity;
            }
        }
    }

    public function getTotal(): float {
        return array_sum(array_map(fn($item) => $item->getTotalPrice(), $this->items));
    }
}