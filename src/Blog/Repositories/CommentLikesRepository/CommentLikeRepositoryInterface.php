<?php

namespace src\Blog\Repositories\CommentLikesRepository;

use src\Blog\CommentLike;
use src\Blog\UUID;

interface CommentLikeRepositoryInterface
{
    public function save(CommentLike $commentLike): void;
    public function getByCommentUuid(UUID $commentUuid): array;
    public function hasLike(UUID $commentUuid, UUID $userUuid): bool;
    public function delete(UUID $uuid): void;
}