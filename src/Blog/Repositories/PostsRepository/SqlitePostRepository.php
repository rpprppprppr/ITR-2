<?php

namespace src\Blog\Repositories\PostsRepository;

use PDO;
use PDOStatement;

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
            ":uuid"=>$post->getId(),
            ":author_uuid"=>$post->getAuthorId(),
            ":title"=>$post->getTitle(),
            ":text"=>$post->getText()
        ]);
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare("SELECT * FROM posts WHERE uuid = :uuid");
        $statement->execute([
            ":uuid"=>$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    public function getByAuthorId(UUID $uuid): Post
    {
        $statement = $this->connection->prepare("SELECT * FROM posts WHERE author_uuid = :author_uuid");
        $statement->execute([
            ":author_uuid"=>$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    public function getPost(PDOStatement $statement, string $postPayload): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException("Post not found: $postPayload");
        }

        return new Post(
            new UUID($result["uuid"]),
            $result["author_uuid"],
            $result["title"],
            $result["text"]
        );
    }
}