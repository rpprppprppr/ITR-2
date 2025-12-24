<?php

namespace src\Blog\Http\Actions\CommentLikes;

use src\Blog\CommentLike;
use src\Blog\UUID;

use src\Blog\Http\Auth\TokenAuthenticationInterface;

use src\Blog\Exceptions\AuthException;
use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\CommentNotFoundException;
use src\Blog\Exceptions\UserNotFoundException;

use src\Blog\Repositories\CommentLikesRepository\CommentLikeRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class AddLikeToComment implements ActionsInterface
{
    public function __construct(
        private CommentLikeRepositoryInterface $commentLikeRepository,
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
            $newCommentLikeUuid = UUID::random();
            $commentUuid = new UUID($request->jsonBodyField('comment_uuid'));
            $userUuid = $user->getId();

            if ($this->commentLikeRepository->hasLike($commentUuid, $userUuid)) {
                return new ErrorResponse("User already liked this comment");
            }

            $commentLike = new CommentLike(
                $newCommentLikeUuid,
                $commentUuid,
                $userUuid
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        } catch (CommentNotFoundException | UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->commentLikeRepository->save($commentLike);

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentLikeUuid
        ]);
    }
}