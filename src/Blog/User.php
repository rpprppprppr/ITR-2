<?php

namespace src\Blog;

use src\Blog\Person\Name;

readonly class User
{
    public function __construct(
        private UUID $uuid,
        private string $username,
        private Name $name
    )
    {}

    public function getId(): UUID
    {
        return $this->uuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return "$this->username as $this->name";
    }
}