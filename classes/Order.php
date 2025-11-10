<?php

require_once 'OrderItem.php';

class Order {
    public int $id;
    public int $user_id;
    public array $items = [];
    public string $status;
    public string $created_at;

    public function __construct(int $id, int $user_id) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->status = "в работе";
        $this->created_at = date('c');
    }

    public function addItem(Product $product, int $quantity): void {
        $this->items[] = new OrderItem($product, $quantity, $product->price);
    }

    public function removeItem(int $product_id): void {
        $this->items = array_filter($this->items, fn($item) => $item->product->id !== $product_id);
    }

    public function calculateTotal(): float {
        return array_sum(array_map(fn($item) => $item->getTotalPrice(), $this->items));
    }

    public function cancelOrder(): void {
        $this->status = "отменен";
        foreach ($this->items as $item) {
            $item->product->updateStock($item->quantity);
        }
    }
}