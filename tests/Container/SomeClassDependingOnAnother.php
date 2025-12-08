<?php

namespace src\Blog\UnitTests\Container;

readonly class SomeClassDependingOnAnother
{
    public function __construct
    (
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two
    )
    {}
}