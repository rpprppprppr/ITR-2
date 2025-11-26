<?php

namespace src\Blog\UnitTests;

use PHPUnit\Framework\TestCase;

use src\Blog\User;
use src\Blog\Person\Name;
use src\Blog\UUID;

class UserTest extends TestCase
{
    private User $user;
    private UUID $uuid;
    private string $username;
    private Name $name;

    protected function setUp(): void
    {
        $this->uuid = UUID::random();
        $this->username = "username";
        $this->name = new Name("first_name", "last_name");

        $this->user = new User(
            $this->uuid,
            $this->username,
            $this->name
        );
    }

    public function testGetId(): void
    {
        $this->assertEquals($this->uuid, $this->user->getId());
    }

    public function testGetUserName(): void
    {
        $this->assertEquals($this->username, $this->user->getUsername());
    }

    public function testGetName(): void
    {
        $this->assertEquals($this->name, $this->user->getName());
    }

    public function testStringRepresentation(): void
    {
        $expectedString = "{$this->username} as {$this->name}";
        $this->assertEquals($expectedString, $this->user->__toString());
    }
}