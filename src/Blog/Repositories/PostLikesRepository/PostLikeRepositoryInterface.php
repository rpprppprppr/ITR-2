<?php

namespace src\Blog\Repositories\PostLikesRepository;

use src\Blog\PostLike;
use src\Blog\UUID;

interface PostLikeRepositoryInterface
{
    public function save(PostLike $postLike): void;
    public function getByPostUuid(UUID $postUuid): array;
    public function hasLike(UUID $postUuid, UUID $userUuid): bool;
    public function delete(UUID $uuid): void;
}