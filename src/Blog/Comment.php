<?php

namespace src\Blog;

readonly class Comment
{
    public function __construct(
        private UUID $uuid,
        private UUID $post_uuid,
        private UUID $author_uuid,
        private string $text
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

    public function getAuthorId(): UUID
    {
        return $this->author_uuid;
    }

    public function getText(): string
    {
        return $this->text;
    }
}