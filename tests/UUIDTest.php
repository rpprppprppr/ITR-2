<?php

namespace src\Blog\UnitTests;

use PHPUnit\Framework\TestCase;

use src\Blog\Exceptions\InvalidArgumentException;

use src\Blog\UUID;

class UUIDTest extends TestCase
{
    public function testInvalidUUID(): void
    {
        $invalidUuidString = 'invalid-uuid-string';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Malformed UUID: $invalidUuidString");

        new UUID($invalidUuidString);
    }

    public function testStringRepresentation(): void
    {
        $uuidString = '123e4567-e89b-12d3-a456-426614174000';
        $uuid = new UUID($uuidString);

        $this->assertEquals($uuidString, $uuid->__toString());
    }
}