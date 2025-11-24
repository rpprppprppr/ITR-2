<?php

namespace src\Blog;

readonly class Post
{
    public function __construct(
        private UUID $uuid,
        private UUID $author_uuid,
        private string $title,
        private string $text
    )
    {}

    public function getId(): UUID
    {
        return $this->uuid;
    }

    public function getAuthorId(): UUID
    {
        return $this->author_uuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }
}