<?php

namespace src\Blog\Http\Actions\Comments;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\CommentNotFoundException;

use src\Blog\Repositories\CommentsRepository\CommentRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;

use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

use src\Blog\UUID;

readonly class FindCommentByUuid implements ActionsInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
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
            $post = $this->commentRepository->get($uuid);
        } catch (CommentNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }


        return new SuccessfulResponse([
            'post_uuid' => (string)$post->getPostId(),
            'author_uuid' => (string)$post->getAuthorId(),
            'text' => $post->getText()
        ]);
    }
}