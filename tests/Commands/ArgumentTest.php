<?php

namespace src\Blog\UnitTests\Commands;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use src\Blog\Commands\Arguments;
use src\Blog\Exceptions\ArgumentException;

class ArgumentTest extends TestCase
{
    static public function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'],
            [' some_string', 'some_string'],
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3']
        ];
    }

    #[DataProvider('argumentsProvider')]
    #[TestDox('Value $inputValue convert to string and equals $expectedValue')]
    public function testItReturnsArgumentsValueByName($inputValue, $expectedValue): void
    {
        $key = 'some_key';

        $arguments = new Arguments([$key => $inputValue]);

        $returnValue = $arguments->get($key);

        $this->assertEquals($expectedValue, $returnValue);
    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        $arguments = new Arguments([]);

        $this->expectException(ArgumentException::class);

        $this->expectExceptionMessage("No such argument: some_key");

        $arguments->get("some_key");
    }
}