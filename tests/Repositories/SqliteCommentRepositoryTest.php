<?php

namespace src\Blog\UnitTests\Repositories;

use PHPUnit\Framework\TestCase;

use PDO;

use src\Blog\Exceptions\CommentNotFoundException;

use src\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use src\Blog\UnitTests\DummyLogger;

use src\Blog\UUID;
use src\Blog\Comment;

class SqliteCommentRepositoryTest extends TestCase
{
    private PDO $connection;
    private SqliteCommentRepository $repository;

    protected function setUp(): void
    {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connection->exec("
            CREATE TABLE comments (
                uuid TEXT,
                post_uuid TEXT,
                author_uuid TEXT,
                text TEXT
            )
        ");

        $this->repository = new SqliteCommentRepository($this->connection, new DummyLogger());
    }

    public function testItSavesCommentToRepository(): void
    {
        $comment = new Comment(
            UUID::random(),
            UUID::random(),
            UUID::random(),
            "Test comment text"
        );

        $this->repository->save($comment);

        $statement = $this->connection->prepare("SELECT * FROM comments WHERE uuid = :uuid");
        $statement->execute([":uuid" => (string)$comment->getId()]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($result);
        $this->assertSame((string)$comment->getId(), $result['uuid']);
        $this->assertSame((string)$comment->getPostId(), $result['post_uuid']);
        $this->assertSame((string)$comment->getAuthorId(), $result['author_uuid']);
        $this->assertSame("Test comment text", $result['text']);
    }

    public function testItFindsCommentByUuid(): void
    {
        $uuid = UUID::random();

        $comment = new Comment(
            $uuid,
            UUID::random(),
            UUID::random(),
            "Hello world"
        );

        $this->connection->prepare("
            INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text)")->execute([
                ":uuid" => (string)$uuid,
                ":post_uuid" => (string)$comment->getPostId(),
                ":author_uuid" => (string)$comment->getAuthorId(),
                ":text" => $comment->getText(),
        ]);

        $found = $this->repository->get($uuid);

        $this->assertSame((string)$uuid, (string)$found->getId());
        $this->assertSame((string)$comment->getPostId(), (string)$found->getPostId());
        $this->assertSame((string)$comment->getAuthorId(), (string)$found->getAuthorId());
        $this->assertSame("Hello world", $found->getText());
    }

    public function testItThrowsExceptionIfCommentNotFound(): void
    {
        $this->expectException(CommentNotFoundException::class);

        $this->repository->get(UUID::random());
    }
}