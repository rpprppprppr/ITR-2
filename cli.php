<?php

use src\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use src\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use src\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use src\Blog\User;
use src\Blog\Person\Name;
use src\Blog\Post;
use src\Blog\Comment;
use src\Blog\UUID;

require_once __DIR__ . "/vendor/autoload.php";

$connection = new PDO("sqlite:" . __DIR__ . "/blog.sqlite");

$userRepository = new SqliteUsersRepository($connection);

$UserUuid1 = UUID::random();
$UserUuid2 = UUID::random();

$userRepository->save(new User($UserUuid1, "vasilii_terkin",new Name("Vasilii", "Terkin")));
$userRepository->save(new User($UserUuid2, "ivan_petrov",new Name("Ivan", "Petrov")));

$PostUuid1 = UUID::random();
$PostUuid2 = UUID::random();

$postRepository = new SqlitePostsRepository($connection);

$postRepository->save(new Post($PostUuid1, $UserUuid1, "Hello world!", "It's a text of post"));
$postRepository->save(new Post($PostUuid2, $UserUuid2, "Goodbye my friends!", "Time is over"));

$commentRepository = new SqliteCommentsRepository($connection);

$commentRepository->save(new Comment(UUID::random(), $PostUuid1, $UserUuid2, "Good job, my friend"));
$commentRepository->save(new Comment(UUID::random(), $PostUuid2, $UserUuid1, "See you later!"));