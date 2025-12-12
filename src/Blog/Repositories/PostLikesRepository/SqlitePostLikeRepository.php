<?php

namespace src\Blog\Repositories\PostLikesRepository;

use PDO;
use Psr\Log\LoggerInterface;

use src\Blog\Exceptions\PostLikeNotFoundException;
use src\Blog\PostLike;
use src\Blog\UUID;

readonly class SqlitePostLikeRepository implements PostLikeRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {}

    public function save(PostLike $postLike): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO postLikes (uuid, post_uuid, user_uuid) 
            VALUES (:uuid, :post_uuid, :user_uuid)
        ");

        $statement->execute([
            ":uuid" => (string)$postLike->getId(),
            ":post_uuid" => (string)$postLike->getPostId(),
            ":user_uuid" => (string)$postLike->getUserId()
        ]);

        $this->logger->info("PostLike saved", ['uuid' => (string)$postLike->getId()]);
    }

    public function getByPostUuid(UUID $postUuid): array
    {
        $statement = $this->connection->prepare("SELECT * FROM postLikes WHERE post_uuid = :post_uuid");
        $statement->execute([
            ':post_uuid' => (string)$postUuid,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $likes = [];

        foreach ($rows as $row) {
            $likes[] = new PostLike(
                new UUID($row['uuid']),
                new UUID($row['post_uuid']),
                new UUID($row['user_uuid'])
            );
        }

        return $likes;
    }

    public function hasLike(UUID $postUuid, UUID $userUuid): bool
    {
        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM postLikes 
            WHERE post_uuid = :post_uuid AND user_uuid = :user_uuid
        ");

        $statement->execute([
            ':post_uuid' => (string)$postUuid,
            ':user_uuid' => (string)$userUuid
        ]);

        return (bool)$statement->fetchColumn();
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare("DELETE FROM postLikes WHERE uuid = :uuid");
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        if ($statement->rowCount() === 0) {
            $this->logger->warning("PostLike not found for deletion", ['uuid' => (string)$uuid]);

            throw new PostLikeNotFoundException("PostLike not found: $uuid");
        }
    }
}