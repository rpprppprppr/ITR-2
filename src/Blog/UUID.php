<?php

namespace src\Blog;

use src\Blog\Exceptions\InvalidArgumentException;

readonly class UUID
{
    public function __construct(
        private string $uuid
    )
    {
        if (!uuid_is_valid($this->uuid)) {
            throw new InvalidArgumentException("Malformed UUID: $uuid");
        }
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }
}