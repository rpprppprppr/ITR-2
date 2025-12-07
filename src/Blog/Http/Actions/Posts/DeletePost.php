<?php

namespace src\Blog\Http\Actions\Posts;

namespace src\Blog\Http\Actions\Posts;

use src\Blog\UUID;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\PostNotFoundException;

use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

class DeletePost implements ActionsInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->postRepository->delete($uuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse("Post not found: " . $e->getMessage());
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return new ErrorResponse("Invalid UUID");
        }

        return new SuccessfulResponse([
            'message' => 'Post deleted successfully',
            'uuid' => (string)$uuid
        ]);
    }
}