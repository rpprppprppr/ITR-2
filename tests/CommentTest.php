<?php

namespace src\Blog\UnitTests;

use PHPUnit\Framework\TestCase;

use src\Blog\Comment;
use src\Blog\UUID;

class CommentTest extends TestCase
{
    private Comment $comment;
    private UUID $uuid;
    private UUID $postUuid;
    private UUID $authorUuid;
    private string $text;

    protected function setUp(): void
    {
        $this->uuid = UUID::random();
        $this->postUuid = UUID::random();
        $this->authorUuid = UUID::random();
        $this->text = 'Test comment text';

        $this->comment = new Comment(
            $this->uuid,
            $this->postUuid,
            $this->authorUuid,
            $this->text
        );
    }

    public function testGetId(): void
    {
        $this->assertEquals($this->uuid, $this->comment->getId());
    }

    public function testGetPostId(): void
    {
        $this->assertEquals($this->postUuid, $this->comment->getPostId());
    }

    public function testGetAuthorId(): void
    {
        $this->assertEquals($this->authorUuid, $this->comment->getAuthorId());
    }

    public function testGetText(): void
    {
        $this->assertEquals($this->text, $this->comment->getText());
    }
}