<?php

class User {
    public int $id;
    public string $name;
    public string $email;
    protected string $password;
    public string $address;

    public function __construct(int $id, string $name, string $email, string $password, string $address) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->address = $address;
    }

    public function login(string $email, string $password): bool {
        return $this->email === $email && $this->password === $password;
    }

    public function getOrders(): array {
        return [];
    }
}

class AdminUser extends User {
    public function manageProducts() {}
    public function manageOrders() {}
}

class CustomerUser extends User {
    public function createOrder(Order $order) {}
}