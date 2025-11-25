<?php

namespace src\Blog\Repositories\CommentsRepository;

use PDO;
use PDOStatement;

use src\Blog\Comment;
use src\Blog\Exceptions\CommentNotFoundException;
use src\Blog\UUID;

readonly class SqliteCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text)
        ");

        $statement->execute([
            ":uuid"=>$comment->getId(),
            ":post_uuid"=>$comment->getPostId(),
            ":author_uuid"=>$comment->getAuthorId(),
            ":text"=>$comment->getText()
        ]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare("SELECT * FROM comments WHERE uuid = :uuid");
        $statement->execute([
            ":uuid"=>$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    public function getByPostId(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare("SELECT * FROM comments WHERE post_uuid = :post_uuid");
        $statement->execute([
            ":post_uuid"=>$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    public function getByAuthorId(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare("SELECT * FROM comments WHERE author_uuid = :author_uuid");
        $statement->execute([
            ":author_uuid"=>$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    public function getComment(PDOStatement $statement, string $commentPayload): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException("Comment not found: $commentPayload");
        }

        return new Comment(
            new UUID($result["uuid"]),
            $result["post_uuid"],
            $result["author_uuid"],
            $result["text"]
        );
    }
}