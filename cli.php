<?php

use src\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use src\Blog\User;
use src\Blog\Person\Name;
use src\Blog\UUID;

require_once __DIR__ . "/vendor/autoload.php";

$connection = new PDO("sqlite:" . __DIR__ . "/blog.sqlite");

$userRepository = new SqliteUsersRepository($connection);

$userRepository->save(new User(UUID::random(), "vasilii_terkin",new Name("Vasilii", "Terkin")));
$userRepository->save(new User(UUID::random(), "ivan_petrov",new Name("Ivan", "Petrov")));