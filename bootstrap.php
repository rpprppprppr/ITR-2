<?php

use src\Blog\Container\DIContainer;

use Dotenv\Dotenv;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;

use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use src\Blog\Repositories\CommentsRepository\CommentRepositoryInterface;
use src\Blog\Repositories\PostLikesRepository\PostLikeRepositoryInterface;
use src\Blog\Repositories\CommentLikesRepository\CommentLikeRepositoryInterface;

use src\Blog\Repositories\UsersRepository\SqliteUserRepository;
use src\Blog\Repositories\PostsRepository\SqlitePostRepository;
use src\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use src\Blog\Repositories\PostLikesRepository\SqlitePostLikeRepository;
use src\Blog\Repositories\CommentLikesRepository\SqliteCommentLikeRepository;

require_once __DIR__ . "/vendor/autoload.php";

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$container->bind(PDO::class, new PDO("sqlite:" . __DIR__ . "/" . $_ENV["SQLITE_DB_PATH"]));

$container->bind(UserRepositoryInterface::class, SqliteUserRepository::class);
$container->bind(PostRepositoryInterface::class, SqlitePostRepository::class);
$container->bind(CommentRepositoryInterface::class, SqliteCommentRepository::class);
$container->bind(PostLikeRepositoryInterface::class, SqlitePostLikeRepository::class);
$container->bind(CommentLikeRepositoryInterface::class, SqliteCommentLikeRepository::class);

$logger = new Logger("blog");

if ($_ENV["LOG_TO_FILES"]) {
    $logger
        ->pushHandler(new StreamHandler(__DIR__ . "/logs/blog.log"))
        ->pushHandler(new StreamHandler(__DIR__ . "/logs/blog.error.log", Level::Error, bubble: false));
}

if ($_ENV["LOG_TO_CONSOLE"]) {
    $logger
        ->pushHandler(new StreamHandler("php://stdout"));
}
$container->bind(LoggerInterface::class,  $logger);

return $container;