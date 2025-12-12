<?php

use Psr\Log\LoggerInterface;

use src\Blog\Commands\Arguments;
use src\Blog\Commands\CreateUserCommand;
use src\Blog\Exceptions\CommandException;

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (CommandException $error) {
    $logger->error($error->getMessage(), ["exception" => $error]);
}