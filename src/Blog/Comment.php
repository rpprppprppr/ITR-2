<?php

namespace src\Blog;

readonly class Comment
{
    public function __construct(
        private int $id,
        private int $authorId,
        private int $articleId,
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

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getText(): string
    {
        return $this->text;
    }
}