<?php

namespace src\Blog\Repositories\CommentsRepository;

use PDO;
use Psr\Log\LoggerInterface;

use src\Blog\Comment;
use src\Blog\Exceptions\CommentNotFoundException;
use src\Blog\UUID;

readonly class SqliteCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {}

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text)
        ");

        $statement->execute([
            ":uuid" => (string)$comment->getId(),
            ":post_uuid" => (string)$comment->getPostId(),
            ":author_uuid" => (string)$comment->getAuthorId(),
            ":text" => $comment->getText()
        ]);

        $this->logger->info("Comment saved", ['uuid' => (string)$comment->getId()]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare("SELECT * FROM comments WHERE uuid = :uuid");
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Comment not found", ['uuid' => (string)$uuid]);

            throw new CommentNotFoundException("Comment not found: $uuid");
        }

        return new Comment(
            new UUID($result['uuid']),
            new UUID($result['post_uuid']),
            new UUID($result['author_uuid']),
            $result['text']
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare("DELETE FROM comments WHERE uuid = :uuid");
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        if ($statement->rowCount() === 0) {
            $this->logger->warning("Comment not found for deletion", ['uuid' => (string)$uuid]);

            throw new CommentNotFoundException("Comment not found: $uuid");
        }
    }
}