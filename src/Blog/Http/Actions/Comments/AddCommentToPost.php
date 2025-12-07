<?php

namespace src\Blog\Http\Actions\Comments;

use src\Blog\Comment;
use src\Blog\UUID;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Exceptions\PostNotFoundException;

use src\Blog\Repositories\CommentsRepository\CommentRepositoryInterface;
use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class AddCommentToPost implements ActionsInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $newCommentUuid = UUID::random();
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));

            $this->postRepository->get($postUuid);
            $this->userRepository->get($authorUuid);

            $comment = new Comment(
                $newCommentUuid,
                $postUuid,
                $authorUuid,
                $request->jsonBodyField('text')
            );
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse("Post not found: " . $exception->getMessage());
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse("Author not found: " . $exception->getMessage());
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->commentRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid
        ]);
    }
}