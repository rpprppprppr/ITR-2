<?php

namespace src\Blog;

use src\Blog\Person\Name;

readonly class User
{
    public function __construct(
        private UUID $uuid,
        private string $username,
        private string $hashedPassword,
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

    private static function hash(string $password, UUID $uuid): string
    {
        return hash("sha256", $uuid . $password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->getHashedPassword() === self::hash($password, $this->getId());
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public static function createForm($username, $password, Name $name): self
    {
        $uuid = UUID::random();

        return new self(
            $uuid,
            $username,
            self::hash($password, $uuid),
            $name
        );
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