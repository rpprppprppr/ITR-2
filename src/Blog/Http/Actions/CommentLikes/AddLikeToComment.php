<?php

namespace src\Blog\Http\Actions\CommentLikes;

use src\Blog\CommentLike;
use src\Blog\UUID;
use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\Request;
use src\Blog\Http\Response;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;

use src\Blog\Repositories\CommentLikesRepository\CommentLikeRepositoryInterface;
use src\Blog\Repositories\CommentsRepository\CommentRepositoryInterface;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\CommentNotFoundException;
use src\Blog\Exceptions\UserNotFoundException;

readonly class AddLikeToComment implements ActionsInterface
{
    public function __construct(
        private CommentLikeRepositoryInterface $commentLikeRepository,
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = new UUID($request->jsonBodyField('comment_uuid'));
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));

            $this->commentRepository->get($commentUuid);
            $this->userRepository->get($userUuid);

            if ($this->commentLikeRepository->hasLike($commentUuid, $userUuid)) {
                return new ErrorResponse("User already liked this comment");
            }

            $commentLike = new CommentLike(
                UUID::random(),
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
            'uuid' => (string)$commentLike->getId()
        ]);
    }
}