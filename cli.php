<?php

use src\Blog\Commands\FakeData\PopulateDB;
use src\Blog\Commands\Users\CreateUser;
use src\Blog\Commands\Users\UpdateUser;
use src\Blog\Commands\Posts\DeletePost;

use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$commandsClasses = [
    PopulateDB::class,
    CreateUser::class,
    UpdateUser::class,
    DeletePost::class,
];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->addCommand($command);
}

$application->run();