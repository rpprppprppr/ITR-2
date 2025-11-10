<?php

abstract class AbstractProduct {
    public int $id;
    public string $name;
    public float $price;
    public string $description;
    protected string $type;

    public function __construct(int $id, string $name, float $price, string $description, string $type) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->type = $type;
    }

    abstract public function calculateFinalCost(float $quantity = 1): float;

    public function getSalesRevenue(float $quantity = 1): float {
        return $this->calculateFinalCost($quantity);
    }

    public function getInfo(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'type' => $this->type
        ];
    }
}