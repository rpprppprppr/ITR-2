<?php

class Category {
    public int $id;
    public string $name;
    public array $products = [];

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function addProduct(Product $product): void {
        $this->products[] = $product;
    }

    public function getAllProducts(): array {
        return $this->products;
    }
}