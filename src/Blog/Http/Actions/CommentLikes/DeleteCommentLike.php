<?php

namespace src\Blog\Http\Actions\CommentLikes;

use src\Blog\UUID;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\CommentLikeNotFoundException;
use src\Blog\Exceptions\InvalidArgumentException;

use src\Blog\Repositories\CommentLikesRepository\CommentLikeRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class DeleteCommentLike implements ActionsInterface
{
    public function __construct(
        private CommentLikeRepositoryInterface $commentLikeRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->commentLikeRepository->delete($uuid);
        } catch (CommentLikeNotFoundException $e) {
            return new ErrorResponse("CommentLike not found: " . $e->getMessage());
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (InvalidArgumentException) {
            return new ErrorResponse("Invalid UUID");
        }

        return new SuccessfulResponse([
            'message' => 'CommentLike deleted successfully',
            'uuid' => (string)$uuid
        ]);
    }
}