<?php

use src\Blog\Commands\Arguments;
use src\Blog\Commands\CreateUserCommand;
use src\Blog\Exceptions\CommandException;

$container = require __DIR__ . './bootstrap.php';

$command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (CommandException $error) {
    echo "{$error->getMessage()}" . PHP_EOL;
}