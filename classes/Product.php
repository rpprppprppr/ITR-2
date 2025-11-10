<?php

class Product {
    public int $id;
    public string $name;
    public float $price;
    public string $description;
    public string $image_url;
    public int $category_id;
    public int $stock;

    public function __construct(int $id, string $name, float $price, string $description, string $image_url, int $category_id, int $stock) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->image_url = $image_url;
        $this->category_id = $category_id;
        $this->stock = $stock;
    }

    public function isAvailable(): bool {
        return $this->stock > 0;
    }

    public function updateStock(int $quantity): void {
        $this->stock += $quantity;
    }

    public function getInfo(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'category_id' => $this->category_id,
            'stock' => $this->stock
        ];
    }
}

class ElectronicProduct extends Product {
    public string $brand;
    public int $warranty;

    public function __construct(...$args) {
        parent::__construct(...array_slice($args, 0, 7));
        $this->brand = $args[7] ?? '';
        $this->warranty = $args[8] ?? 0;
    }
}

class ClothingProduct extends Product {
    public string $size;
    public string $color;
    public string $material;

    public function __construct(...$args) {
        parent::__construct(...array_slice($args, 0, 7));
        $this->size = $args[7] ?? '';
        $this->color = $args[8] ?? '';
        $this->material = $args[9] ?? '';
    }
}