<?php

namespace src\Blog;

readonly class CommentLike
{
    public function __construct(
        private UUID $uuid,
        private UUID $comment_uuid,
        private UUID $user_uuid
    )
    {}

    public function getId(): UUID
    {
        return $this->uuid;
    }

    public function getCommentId(): UUID
    {
        return $this->comment_uuid;
    }

    public function getUserId(): UUID
    {
        return $this->user_uuid;
    }
}