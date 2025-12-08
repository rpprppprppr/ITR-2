<?php

namespace src\Blog;

readonly class PostLike
{
    public function __construct(
        private UUID $uuid,
        private UUID $post_uuid,
        private UUID $user_uuid
    )
    {}

    public function getId(): UUID
    {
        return $this->uuid;
    }

    public function getPostId(): UUID
    {
        return $this->post_uuid;
    }

    public function getUserId(): UUID
    {
        return $this->user_uuid;
    }
}