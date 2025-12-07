<?php

namespace src\Blog\Http\Actions\Posts;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\PostNotFoundException;

use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;

use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

use src\Blog\UUID;

readonly class FindPostByUuid implements ActionsInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $post = $this->postRepository->get($uuid);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }


        return new SuccessfulResponse([
            'author_uuid' => (string)$post->getAuthorId(),
            'title' => $post->getTitle(),
            'text' => $post->getText()
        ]);
    }
}