<?php

use src\Blog\Exceptions\HttpException;

use src\Blog\Http\Actions\Users\CreateUser;
use src\Blog\Http\Actions\Users\FindByUsername;
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

$routes = [
    "GET" => [
        '/users/show' => new FindByUsername(new SqliteUserRepository(new PDO("sqlite:" . __DIR__ . "/blog.sqlite"))),
//        '/posts/show' => new FindByUsername(new SqlitePostRepository(new PDO("sqlite:" . __DIR__ . "/blog.sqlite"))),
//        '/comments/show' => new FindByUsername(new SqliteCommentRepository(new PDO("sqlite:" . __DIR__ . "/blog.sqlite")))
    ],
    'POST' => [
        '/users/create' => new CreateUser(new SqliteUserRepository(new PDO("sqlite:" . __DIR__ . "/blog.sqlite"))),
//        '/posts/create' => new CreatePost(new SqlitePostRepository(new PDO("sqlite:" . __DIR__ . "/blog.sqlite"))),
//        '/comments/create' => new CreateComment(new SqliteCommentRepository(new PDO("sqlite:" . __DIR__ . "/blog.sqlite")))
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
