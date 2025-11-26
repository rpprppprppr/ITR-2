<?php

namespace src\Blog\UnitTests\Commands;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

use src\Blog\Exceptions\ArgumentException;

use src\Blog\Commands\Arguments;

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

    public function testItSkipsEmptyStringValue(): void
    {
        $iterator = new \ArrayIterator([
            'empty' => '',
            'key1' => 'value1',
        ]);

        $arguments = new Arguments($iterator);

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument: empty");
        $arguments->get('empty', '');

        $this->assertEquals('value1', $arguments->get('key1'));
    }

    public function testItCreatesFromArgvWithValidArguments(): void
    {
        $argv = [
            'username=ivan_ivanov',
            'first_name=ivan',
            'last_name=ivanov'
        ];

        $arguments = Arguments::fromArgv($argv);

        $this->assertEquals('ivan_ivanov', $arguments->get('username'));
        $this->assertEquals('ivan', $arguments->get('first_name'));
        $this->assertEquals('ivanov', $arguments->get('last_name'));
    }

    public function testItSkipsInvalidArgumentsWithoutEquals(): void
    {
        $argv = [
            'username=ivan_ivanov',
            'first_name ivan',
            'last_name=ivanov'
        ];

        $arguments = Arguments::fromArgv($argv);

        $this->assertEquals('ivan_ivanov', $arguments->get('username'));

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument: first_name");
        $arguments->get('first_name');

        $this->assertEquals('ivanov', $arguments->get('last_name'));
    }

    public function testItSkipsArgumentsWithMultipleEquals(): void
    {
        $argv = [
            'username=ivan_ivanov',
            'first_name=ivan',
            'last_name=ivanov=ivanovich'
        ];

        $arguments = Arguments::fromArgv($argv);

        $this->assertEquals('ivan_ivanov', $arguments->get('username'));
        $this->assertEquals('ivan', $arguments->get('first_name'));

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage("No such argument: last_name");
        $arguments->get('last_name');
    }

    public function testItTrimsWhitespaceFromValues(): void
    {
        $argv = [
            'username=ivan_ivanov',
            'first_name=   ivan   ',
            'last_name=ivanov   '
        ];

        $arguments = Arguments::fromArgv($argv);

        $this->assertEquals('ivan_ivanov', $arguments->get('username'));
        $this->assertEquals('ivan', $arguments->get('first_name'));
        $this->assertEquals('ivanov', $arguments->get('last_name'));
    }
}