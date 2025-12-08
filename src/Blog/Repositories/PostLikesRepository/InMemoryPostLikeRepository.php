<?php

namespace src\Blog\Repositories\PostLikesRepository;

use src\Blog\UUID;
use src\Blog\PostLike;
use src\Blog\Exceptions\PostLikeNotFoundException;

class InMemoryPostLikeRepository implements PostLikeRepositoryInterface
{
    private array $postLikes = [];

    public function save(PostLike $postLike): void
    {
        foreach ($this->postLikes as $existing) {
            if (
                (string)$existing->getPostId() === (string)$postLike->getPostId() &&
                (string)$existing->getUserId() === (string)$postLike->getUserId()
            ) {
                return;
            }
        }

        $this->postLikes[] = $postLike;
    }

    public function getByPostUuid(UUID $postUuid): array
    {
        $likes = [];

        foreach ($this->postLikes as $postLike) {
            if ((string)$postLike->getPostId() === (string)$postUuid) {
                $likes[] = $postLike;
            }
        }

        return $likes;
    }

    public function hasLike(UUID $postUuid, UUID $userUuid): bool
    {
        foreach ($this->postLikes as $postLike) {
            if (
                (string)$postLike->getPostId() === (string)$postUuid &&
                (string)$postLike->getUserId() === (string)$userUuid
            ) {
                return true;
            }
        }
        return false;
    }

    public function delete(UUID $uuid): void
    {
        foreach ($this->postLikes as $index => $postLike) {
            if ((string)$postLike->getId() === (string)$uuid) {
                unset($this->postLikes[$index]);
                $this->postLikes = array_values($this->postLikes);
                return;
            }
        }

        throw new PostLikeNotFoundException("PostLike not found: $uuid");
    }
}