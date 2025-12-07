<?php

namespace src\Blog\Repositories\CommentsRepository;

use PDO;

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
            ':uuid' => (string)$uuid,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
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
            throw new CommentNotFoundException("Comment not found: $uuid");
        }
    }
}