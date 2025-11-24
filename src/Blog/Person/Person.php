<?php

namespace src\Blog\Person;

use DateTimeImmutable;

readonly class Person
{
    private function __construct(
        private Name $name,
        private DateTimeImmutable $registeredOn
    )
    {}

    public function __toString()
    {
        return $this->name . '(на сайте с ' . $this->registeredOn->format('Y.m.d') . ')';
    }

}