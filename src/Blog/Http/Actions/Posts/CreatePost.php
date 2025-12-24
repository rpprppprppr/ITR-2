<?php

namespace src\Blog\Http\Actions\Posts;

use Psr\Log\LoggerInterface;
use src\Blog\Http\Auth\TokenAuthenticationInterface;
use src\Blog\Post;
use src\Blog\UUID;

use src\Blog\Exceptions\AuthException;
use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\InvalidArgumentException;
use src\Blog\Exceptions\UserNotFoundException;

use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use src\Blog\Http\Auth\AuthenticationInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class CreatePost implements ActionsInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private TokenAuthenticationInterface $authentication
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $error) {
            return new ErrorResponse($error->getMessage());
        }

        try {
            $newPostUuid = UUID::random();
            $authorUuid = $user->getId();
            $post = new Post(
                $newPostUuid,
                $authorUuid,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text')
            );
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse("Author not found: " . $exception->getMessage());
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postRepository->save($post);

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid
        ]);
    }
}