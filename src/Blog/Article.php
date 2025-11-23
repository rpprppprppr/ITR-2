<?php

namespace src\Blog;

readonly class Article
{
    public function __construct(
        private int $id,
        private int $authorId,
        private string $title,
        private string $text
    )
    {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
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