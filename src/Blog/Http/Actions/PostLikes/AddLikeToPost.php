<?php

namespace src\Blog\Http\Actions\PostLikes;

use src\Blog\PostLike;
use src\Blog\UUID;
use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\Request;
use src\Blog\Http\Response;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;

use src\Blog\Repositories\PostLikesRepository\PostLikeRepositoryInterface;
use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\PostNotFoundException;
use src\Blog\Exceptions\UserNotFoundException;

readonly class AddLikeToPost implements ActionsInterface
{
    public function __construct(
        private PostLikeRepositoryInterface $postLikeRepository,
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));

            $this->postRepository->get($postUuid);
            $this->userRepository->get($userUuid);

            if ($this->postLikeRepository->hasLike($postUuid, $userUuid)) {
                return new ErrorResponse("User already liked this post");
            }

            $postLike = new PostLike(
                UUID::random(),
                $postUuid,
                $userUuid
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        } catch (PostNotFoundException | UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postLikeRepository->save($postLike);

        return new SuccessfulResponse([
            'uuid' => (string)$postLike->getId()
        ]);
    }
}