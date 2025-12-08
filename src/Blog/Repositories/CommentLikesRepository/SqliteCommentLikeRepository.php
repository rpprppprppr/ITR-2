<?php

namespace src\Blog\Repositories\CommentLikesRepository;

use PDO;

use src\Blog\Exceptions\CommentLikeNotFoundException;

use src\Blog\CommentLike;
use src\Blog\UUID;

readonly class SqliteCommentLikeRepository implements CommentLikeRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    public function save(CommentLike $commentLike): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO commentLikes (uuid, comment_uuid, user_uuid) 
            VALUES (:uuid, :comment_uuid, :user_uuid)
        ");

        $statement->execute([
            ":uuid"=>(string)$commentLike->getId(),
            ":comment_uuid"=>(string)$commentLike->getCommentId(),
            ":user_uuid"=>(string)$commentLike->getUserId()
        ]);
    }

    public function getByCommentUuid(UUID $commentUuid): array
    {
        $statement = $this->connection->prepare("SELECT * FROM commentLikes WHERE comment_uuid = :comment_uuid");
        $statement->execute([
            ':comment_uuid' => (string)$commentUuid,
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $likes = [];

        foreach ($rows as $row) {
            $likes[] = new CommentLike(
                new UUID($row['uuid']),
                new UUID($row['comment_uuid']),
                new UUID($row['user_uuid'])
            );
        }
        return $likes;
    }

    public function hasLike(UUID $commentUuid, UUID $userUuid): bool
    {
        $statement = $this->connection->prepare("
            SELECT COUNT(*) FROM commentLikes 
            WHERE comment_uuid  = :comment_uuid AND user_uuid = :user_uuid
        ");

        $statement->execute([
            ':comment_uuid' => (string)$commentUuid,
            ':user_uuid' => (string)$userUuid
        ]);

        return (bool)$statement->fetchColumn();
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare("DELETE FROM commentLikes WHERE uuid = :uuid");
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        if ($statement->rowCount() === 0) {
            throw new CommentLikeNotFoundException("CommentLike not found: $uuid");
        }
    }
}