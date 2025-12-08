<?php

use src\Blog\Container\DIContainer;

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

$container = new DIContainer();

$container->bind(PDO::class, new PDO("sqlite:" . __DIR__ . "/blog.sqlite"));

$container->bind(UserRepositoryInterface::class, SqliteUserRepository::class);
$container->bind(PostRepositoryInterface::class, SqlitePostRepository::class);
$container->bind(CommentRepositoryInterface::class, SqliteCommentRepository::class);
$container->bind(PostLikeRepositoryInterface::class, SqlitePostLikeRepository::class);
$container->bind(CommentLikeRepositoryInterface::class, SqliteCommentLikeRepository::class);

return $container;