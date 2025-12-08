<?php

namespace src\Blog\Repositories\CommentLikesRepository;

use src\Blog\UUID;
use src\Blog\CommentLike;
use src\Blog\Exceptions\CommentLikeNotFoundException;

class InMemoryCommentLikeRepository implements CommentLikeRepositoryInterface
{
    private array $commentLikes = [];

    public function save(CommentLike $commentLike): void
    {
        foreach ($this->commentLikes as $existing) {
            if (
                (string)$existing->getCommentId() === (string)$commentLike->getCommentId() &&
                (string)$existing->getUserId() === (string)$commentLike->getUserId()
            ) {
                return;
            }
        }

        $this->commentLikes[] = $commentLike;
    }

    public function getByCommentUuid(UUID $commentUuid): array
    {
        $likes = [];

        foreach ($this->commentLikes as $postLike) {
            if ((string)$postLike->getPostId() === (string)$commentUuid) {
                $likes[] = $postLike;
            }
        }

        return $likes;
    }

    public function hasLike(UUID $commentUuid, UUID $userUuid): bool
    {
        foreach ($this->commentLikes as $commentLike) {
            if (
                (string)$commentLike->getCommentId() === (string)$commentUuid &&
                (string)$commentLike->getUserId() === (string)$userUuid
            ) {
                return true;
            }
        }
        return false;
    }

    public function delete(UUID $uuid): void
    {
        foreach ($this->commentLikes as $index => $commentLike) {
            if ((string)$commentLike->getId() === (string)$uuid) {
                unset($this->commentLikes[$index]);
                $this->commentLikes = array_values($this->commentLikes);
                return;
            }
        }

        throw new CommentLikeNotFoundException("CommentLike not found: $uuid");
    }
}