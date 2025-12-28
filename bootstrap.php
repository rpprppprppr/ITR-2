<?php

use src\Blog\Container\DIContainer;

use Dotenv\Dotenv;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;

use Faker\Generator;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\Lorem;

use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\Repositories\UsersRepository\SqliteUserRepository;

use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use src\Blog\Repositories\PostsRepository\SqlitePostRepository;

use src\Blog\Repositories\CommentsRepository\CommentRepositoryInterface;
use src\Blog\Repositories\CommentsRepository\SqliteCommentRepository;

use src\Blog\Repositories\PostLikesRepository\PostLikeRepositoryInterface;
use src\Blog\Repositories\PostLikesRepository\SqlitePostLikeRepository;

use src\Blog\Repositories\CommentLikesRepository\CommentLikeRepositoryInterface;
use src\Blog\Repositories\CommentLikesRepository\SqliteCommentLikeRepository;

use src\Blog\Http\Auth\PasswordAuthenticationInterface;
use src\Blog\Http\Auth\PasswordAuthentication;

use src\Blog\Repositories\AuthTokenRepository\AuthTokenRepositoryInterface;
use src\Blog\Repositories\AuthTokenRepository\SqliteAuthTokenRepository;

use src\Blog\Http\Auth\TokenAuthenticationInterface;
use src\Blog\Http\Auth\BearerTokenAuthentication;

require_once __DIR__ . "/vendor/autoload.php";

Dotenv::createImmutable(__DIR__)->safeLoad();

$faker = new Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container = new DIContainer();

$container->bind(PDO::class, new PDO("sqlite:" . __DIR__ . "/" . $_ENV["SQLITE_DB_PATH"]));

$container->bind(UserRepositoryInterface::class, SqliteUserRepository::class);
$container->bind(PostRepositoryInterface::class, SqlitePostRepository::class);
$container->bind(CommentRepositoryInterface::class, SqliteCommentRepository::class);
$container->bind(PostLikeRepositoryInterface::class, SqlitePostLikeRepository::class);
$container->bind(CommentLikeRepositoryInterface::class, SqliteCommentLikeRepository::class);

$container->bind(PasswordAuthenticationInterface::class, PasswordAuthentication::class);
$container->bind(AuthTokenRepositoryInterface::class, SqliteAuthTokenRepository::class);
$container->bind(TokenAuthenticationInterface::class, BearerTokenAuthentication::class);

$container->bind(Generator::class, $faker);

$logger = new Logger("blog");
$isCli = php_sapi_name() === 'cli';

if ($_ENV["LOG_TO_FILES"]) {
    $logger
        ->pushHandler(new StreamHandler(__DIR__ . "/logs/blog.log"))
        ->pushHandler(new StreamHandler(__DIR__ . "/logs/blog.error.log", Level::Error, bubble: false));
}

if ($_ENV["LOG_TO_CONSOLE"] && !$isCli) {
    $logger->pushHandler(new StreamHandler("php://stdout", Level::Warning));
}

$container->bind(LoggerInterface::class,  $logger);

return $container;