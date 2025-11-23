<?php

namespace src\Blog;

readonly class User
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName
    )
    {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}