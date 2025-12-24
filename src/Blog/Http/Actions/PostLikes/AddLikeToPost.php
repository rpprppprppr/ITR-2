<?php

namespace src\Blog\Http\Actions\PostLikes;

use src\Blog\PostLike;
use src\Blog\UUID;

use src\Blog\Http\Auth\TokenAuthenticationInterface;

use src\Blog\Exceptions\AuthException;
use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\PostNotFoundException;
use src\Blog\Exceptions\UserNotFoundException;

use src\Blog\Repositories\PostLikesRepository\PostLikeRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class AddLikeToPost implements ActionsInterface
{
    public function __construct(
        private PostLikeRepositoryInterface $postLikeRepository,
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
            $newPostLikeUuid = UUID::random();
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $userUuid = $user->getId();

            if ($this->postLikeRepository->hasLike($postUuid, $userUuid)) {
                return new ErrorResponse("User already liked this post");
            }

            $postLike = new PostLike(
                $newPostLikeUuid,
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