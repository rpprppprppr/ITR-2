<?php

namespace src\Blog\UnitTests\Repositories;

use PHPUnit\Framework\TestCase;

use PDO;

use src\Blog\Exceptions\PostNotFoundException;

use src\Blog\Repositories\PostsRepository\SqlitePostRepository;
use src\Blog\UnitTests\DummyLogger;

use src\Blog\UUID;
use src\Blog\Post;

class SqlitePostRepositoryTest extends TestCase
{
    private PDO $connection;
    private SqlitePostRepository $repository;

    protected function setUp(): void
    {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connection->exec("
            CREATE TABLE posts (
                uuid TEXT,
                author_uuid TEXT,
                title TEXT,
                text TEXT
            )
        ");

        $this->repository = new SqlitePostRepository($this->connection, new DummyLogger());
    }

    public function testItSavesPostToRepository(): void
    {
        $post = new Post(
            UUID::random(),
            UUID::random(),
            "Test title",
            "Test text"
        );

        $this->repository->save($post);

        $statement = $this->connection->prepare("SELECT * FROM posts WHERE uuid = :uuid");
        $statement->execute([
            ":uuid" => (string)$post->getId(),
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($result);
        $this->assertSame((string)$post->getId(), $result["uuid"]);
        $this->assertSame((string)$post->getAuthorId(), $result["author_uuid"]);
        $this->assertSame("Test title", $result["title"]);
        $this->assertSame("Test text", $result["text"]);
    }

    public function testItFindsPostByUuid(): void
    {
        $uuid = UUID::random();

        $post = new Post(
            $uuid,
            UUID::random(),
            "Hello",
            "World"
        );

        $this->connection->prepare("
            INSERT INTO posts (uuid, author_uuid, title, text)
            VALUES (:uuid, :author_uuid, :title, :text)")->execute([
                ":uuid" => (string)$post->getId(),
                ":author_uuid" => (string)$post->getAuthorId(),
                ":title" => $post->getTitle(),
                ":text" => $post->getText(),
        ]);

        $found = $this->repository->get($uuid);

        $this->assertSame((string)$uuid, (string)$found->getId());
        $this->assertSame((string)$post->getAuthorId(), (string)$found->getAuthorId());
        $this->assertSame($post->getTitle(), $found->getTitle());
        $this->assertSame($post->getText(), $found->getText());
    }

    public function testItThrowsExceptionIfPostNotFound(): void
    {
        $this->expectException(PostNotFoundException::class);
        $this->repository->get(UUID::random());
    }
}