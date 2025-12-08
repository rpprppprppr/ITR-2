<?php

namespace src\Blog\Http\Actions\PostLikes;

use src\Blog\UUID;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\PostLikeNotFoundException;
use src\Blog\Exceptions\InvalidArgumentException;

use src\Blog\Repositories\PostLikesRepository\PostLikeRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class DeletePostLike implements ActionsInterface
{
    public function __construct(
        private PostLikeRepositoryInterface $postLikeRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->postLikeRepository->delete($uuid);
        } catch (PostLikeNotFoundException $e) {
            return new ErrorResponse("PostLike not found: " . $e->getMessage());
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (InvalidArgumentException) {
            return new ErrorResponse("Invalid UUID");
        }

        return new SuccessfulResponse([
            'message' => 'PostLike deleted successfully',
            'uuid' => (string)$uuid
        ]);
    }
}