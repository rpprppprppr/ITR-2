<?php

use src\Blog\Exceptions\HttpException;

use src\Blog\Http\Actions\Users\CreateUser;
use src\Blog\Http\Actions\Users\FindByUsername;
use src\Blog\Http\Actions\Users\DeleteUser;

use src\Blog\Http\Actions\Posts\CreatePost;
use src\Blog\Http\Actions\Posts\FindPostByUuid;
use src\Blog\Http\Actions\Posts\DeletePost;

use src\Blog\Http\Actions\Comments\AddCommentToPost;
use src\Blog\Http\Actions\Comments\FindCommentByUuid;
use src\Blog\Http\Actions\Comments\DeleteComment;

use src\Blog\Http\Request;
use src\Blog\Http\ErrorResponse;

use src\Blog\Repositories\UsersRepository\SqliteUserRepository;
use src\Blog\Repositories\PostsRepository\SqlitePostRepository;
use src\Blog\Repositories\CommentsRepository\SqliteCommentRepository;

require_once __DIR__ . "/vendor/autoload.php";

$request = new Request($_GET, $_SERVER, file_get_contents('php://input') );

try {
    $path = $request->path();
} catch (HttpException) {
    new ErrorResponse()->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    new ErrorResponse()->send();
    return;
}

$pdo = new PDO("sqlite:" . __DIR__ . "/blog.sqlite");
$routes = [
    "GET" => [
        '/users/show' => new FindByUsername(new SqliteUserRepository($pdo)),
        '/posts/show' => new FindPostByUuid(new SqlitePostRepository($pdo)),
        '/comments/show' => new FindCommentByUuid(new SqliteCommentRepository($pdo))
    ],
    'POST' => [
        '/users/create' => new CreateUser(new SqliteUserRepository($pdo)),
        '/posts/create' => new CreatePost(
            new SqlitePostRepository($pdo),
            new SqliteUserRepository($pdo)
        ),
        '/posts/comment' => new AddCommentToPost(
            new SqliteCommentRepository($pdo),
            new SqlitePostRepository($pdo),
            new SqliteUserRepository($pdo)
        )
    ],
    'DELETE' => [
        '/users' => new DeleteUser(new SqliteUserRepository($pdo)),
        '/posts' => new DeletePost(new SqlitePostRepository($pdo)),
        '/comments' => new DeleteComment(new SqliteCommentRepository($pdo))
    ]
];

if (!array_key_exists($method, $routes)) {
    new ErrorResponse("Not found")->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    new ErrorResponse("Not found")->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (Exception $error) {
    new ErrorResponse($error->getMessage())->send();
}

$response->send();
