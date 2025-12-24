<?php

namespace src\Blog\Repositories\AuthTokenRepository;

use PDO;
use DateTimeImmutable;

use src\Blog\UUID;
use src\Blog\AuthToken;

use Exception;
use PDOException;
use src\Blog\Exceptions\AuthTokenRepositoryException;

readonly class SqliteAuthTokenRepository implements AuthTokenRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    public function save(AuthToken $authToken): void
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO tokens (token, user_uuid, expires_on) 
                VALUES (:token, :user_uuid, :expires_on)
                ON CONFLICT (token) DO UPDATE SET expires_on = :expires_on
            ");

            $statement->execute([
                ":token" => $authToken->getToken(),
                ":user_uuid" => (string)$authToken->getUserUuid(),
                ":expires_on" => (string)$authToken->getExpiresOn()->format(DateTimeImmutable::ATOM),
            ]);
        } catch (PDOException $error) {
            throw new AuthTokenRepositoryException($error->getMessage(), (int)$error->getCode(), $error);
        }
    }

    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare("
                SELECT * FROM tokens WHERE token = :token
            ");

            $statement->execute([
                ":token" => $token,
            ]);

            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            throw new AuthTokenRepositoryException($error->getMessage(), (int)$error->getCode(), $error);
        }

        if (!$result) {
            throw new AuthTokenRepositoryException("Cannot find token: $token");
        }

        try {
            return new AuthToken(
                $result["token"],
                new UUID($result["user_uuid"]),
                new DateTimeImmutable($result["expires_on"]),
            );
        } catch (Exception $error) {
            throw new AuthTokenRepositoryException($error->getMessage(), (int)$error->getCode(), $error);
        }
    }
}