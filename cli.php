<?php

use src\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use src\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use src\Blog\User;
use src\Blog\Post;
use src\Blog\Person\Name;
use src\Blog\UUID;

require_once __DIR__ . "/vendor/autoload.php";

$connection = new PDO("sqlite:" . __DIR__ . "/blog.sqlite");

$userRepository = new SqliteUsersRepository($connection);

$uuid1 = UUID::random();
$uuid2 = UUID::random();

$userRepository->save(new User($uuid1, "vasilii_terkin",new Name("Vasilii", "Terkin")));
$userRepository->save(new User($uuid2, "ivan_petrov",new Name("Ivan", "Petrov")));

$postRepository = new SqlitePostsRepository($connection);

$postRepository->save(new Post(UUID::random(), $uuid1, "Hello world!", "It's a text of post"));
$postRepository->save(new Post(UUID::random(), $uuid2, "Goodbye my friends!", "Time is over"));