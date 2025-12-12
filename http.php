<?php

use Psr\Log\LoggerInterface;

use src\Blog\Exceptions\HttpException;

use src\Blog\Http\Actions\Users\CreateUser;
use src\Blog\Http\Actions\Users\FindByUsername;
use src\Blog\Http\Actions\Users\DeleteUser;

use src\Blog\Http\Actions\Posts\CreatePost;
use src\Blog\Http\Actions\Posts\DeletePost;

use src\Blog\Http\Actions\Comments\AddCommentToPost;
use src\Blog\Http\Actions\Comments\DeleteComment;

use src\Blog\Http\Actions\PostLikes\AddLikeToPost;
use src\Blog\Http\Actions\PostLikes\DeletePostLike;

use src\Blog\Http\Actions\CommentLikes\AddLikeToComment;
use src\Blog\Http\Actions\CommentLikes\DeleteCommentLike;

use src\Blog\Http\Request;
use src\Blog\Http\ErrorResponse;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input') );

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $error) {
    $logger->warning($error->getMessage());
    new ErrorResponse()->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $error) {
    $logger->warning($error->getMessage());
    new ErrorResponse()->send();
    return;
}

$routes = [
    "GET" => [
        '/users/show' => FindByUsername::class,
    ],
    'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => AddCommentToPost::class,
        '/posts/like' => AddLikeToPost::class,
        '/comments/like' => AddLikeToComment::class
    ],
    'DELETE' => [
        '/users' => DeleteUser::class,
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
        '/postlikes' => DeletePostLike::class,
        '/commentlikes' => DeleteCommentLike::class
    ]
];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    new ErrorResponse($message)->send();
    return;
}

$actionClassName = $routes[$method][$path];


try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (Exception $error) {
    $logger->error($error->getMessage(), ['exception' => $error]);
    new ErrorResponse($error->getMessage())->send();
}

$response->send();