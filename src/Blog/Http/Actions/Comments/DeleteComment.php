<?php

namespace src\Blog\Http\Actions\Comments;

use src\Blog\UUID;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\CommentNotFoundException;
use src\Blog\Exceptions\InvalidArgumentException;

use src\Blog\Repositories\CommentsRepository\CommentRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

class DeleteComment implements ActionsInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->commentRepository->delete($uuid);
        } catch (CommentNotFoundException $e) {
            return new ErrorResponse("Comment not found: " . $e->getMessage());
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (InvalidArgumentException $e) {
            return new ErrorResponse("Invalid UUID");
        }

        return new SuccessfulResponse([
            'message' => 'Comment deleted successfully',
            'uuid' => (string)$uuid
        ]);
    }
}