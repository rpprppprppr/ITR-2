<?php

use src\Blog\Repositories\UsersRepository\SqliteUserRepository;

use src\Blog\Commands\Arguments;
use src\Blog\Commands\CreateUserCommand;
use src\Blog\Exceptions\CommandException;

require_once __DIR__ . "/vendor/autoload.php";

$connection = new PDO("sqlite:" . __DIR__ . "/blog.sqlite");

$userRepository = new SqliteUserRepository($connection);

$command = new CreateUserCommand($userRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (CommandException $error) {
    echo "{$error->getMessage()}" . PHP_EOL;
}