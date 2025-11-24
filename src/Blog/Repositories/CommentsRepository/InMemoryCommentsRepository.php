<?php

namespace src\Blog\Repositories\CommentsRepository;

use src\Blog\Exceptions\CommentNotFoundException;
use src\Blog\Comment;
use src\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use src\Blog\UUID;

class InMemoryCommentsRepository implements CommentsRepositoryInterface
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

    public function getByPostId(UUID $uuid): Comment
    {
        foreach ($this->comments as $comment) {
            if ((string)$comment->getPostId() === (string)$uuid) {
                return $comment;
            }
        }

        throw new CommentNotFoundException("Comment not found: $uuid (postId)");
    }

    public function getByAuthorId(UUID $uuid): Comment
    {
        foreach ($this->comments as $comment) {
            if ((string)$comment->getAuthorId() === (string)$uuid) {
                return $comment;
            }
        }

        throw new CommentNotFoundException("Comment not found: $uuid (authorId)");
    }
}