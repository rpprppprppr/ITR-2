<?php

namespace src\Blog\Repositories\PostsRepository;

use PDO;

use src\Blog\Post;
use src\Blog\Exceptions\PostNotFoundException;
use src\Blog\UUID;

readonly class SqlitePostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO posts (uuid, author_uuid, title, text) 
            VALUES (:uuid, :author_uuid, :title, :text)
        ");

        $statement->execute([
            ":uuid" => (string)$post->getId(),
            ":author_uuid" => (string)$post->getAuthorId(),
            ":title"=>$post->getTitle(),
            ":text"=>$post->getText()
        ]);
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare("SELECT * FROM posts WHERE uuid = :uuid");
        $statement->execute([
            ":uuid" => (string)$uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException("Post not found: $uuid");
        }

        return new Post(
            new UUID($result["uuid"]),
            new UUID($result["author_uuid"]),
            $result["title"],
            $result["text"]
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare("DELETE FROM posts WHERE uuid = :uuid");
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        if ($statement->rowCount() === 0) {
            throw new PostNotFoundException("Post not found: $uuid");
        }
    }
}