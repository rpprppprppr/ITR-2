<?php

namespace src\Blog\Http\Actions\Posts;

use src\Blog\Post;
use src\Blog\UUID;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\InvalidArgumentException;
use src\Blog\Exceptions\UserNotFoundException;

use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class CreatePost implements ActionsInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $newPostUuid = UUID::random();
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));

            $this->userRepository->get($authorUuid);

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