<?php

namespace src\Blog\UnitTests;

use PHPUnit\Framework\TestCase;

use src\Blog\Post;
use src\Blog\UUID;

class PostTest extends TestCase
{
    private Post $post;
    private UUID $uuid;
    private UUID $authorUuid;
    private string $title;
    private string $text;

    protected function setUp(): void
    {
        $this->uuid = UUID::random();
        $this->authorUuid = UUID::random();
        $this->title = 'Test comment title';
        $this->text = 'Test comment text';

        $this->post = new Post(
            $this->uuid,
            $this->authorUuid,
            $this->title,
            $this->text
        );
    }

    public function testGetId(): void
    {
        $this->assertEquals($this->uuid, $this->post->getId());
    }

    public function testGetAuthorId(): void
    {
        $this->assertEquals($this->authorUuid, $this->post->getAuthorId());
    }

    public function testGetTitle(): void
    {
        $this->assertEquals($this->title, $this->post->getTitle());
    }

    public function testGetText(): void
    {
        $this->assertEquals($this->text, $this->post->getText());
    }
}