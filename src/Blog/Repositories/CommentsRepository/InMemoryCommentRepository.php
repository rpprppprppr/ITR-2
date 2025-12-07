<?php

namespace src\Blog\Repositories\CommentsRepository;

use src\Blog\Exceptions\CommentNotFoundException;
use src\Blog\Comment;
use src\Blog\UUID;

class InMemoryCommentRepository implements CommentRepositoryInterface
{
    private array $comments = [];

    public function save(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    public function get(UUID $uuid): Comment
    {
        foreach ($this->comments as $comment) {
            if ((string)$comment->getId() === (string)$uuid) {
                return $comment;
            }
        }

        throw new CommentNotFoundException("Comment not found: $uuid");
    }
}