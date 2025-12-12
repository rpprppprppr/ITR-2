<?php

namespace src\Blog\Repositories\UsersRepository;

use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

use src\Blog\Person\Name;
use src\Blog\User;
use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\UUID;

readonly class SqliteUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {}

    public function save(User $user): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO users (uuid, username, first_name, last_name) 
            VALUES (:uuid, :username, :first_name, :last_name)
        ");

        $statement->execute([
            ":uuid" => $user->getId(),
            ":username" => $user->getUsername(),
            ":first_name" => $user->getName()->getFirstName(),
            ":last_name" => $user->getName()->getLastName()
        ]);

        $this->logger->info("User saved", ['uuid' => (string)$user->getId()]);
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare("SELECT * FROM users WHERE uuid = :uuid");
        $statement->execute([
            ":uuid" => (string)$uuid,
        ]);

        return $this->getUserFromStatement($statement, (string)$uuid);
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare("SELECT * FROM users WHERE username = :username");
        $statement->execute([
            ":username" => $username,
        ]);

        return $this->getUserFromStatement($statement, $username);
    }

    private function getUserFromStatement(PDOStatement $statement, string $identifier): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("User not found", ['identifier' => $identifier]);

            throw new UserNotFoundException("User not found: $identifier");
        }

        return new User(
            new UUID($result["uuid"]),
            $result["username"],
            new Name($result["first_name"], $result["last_name"])
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare("DELETE FROM users WHERE uuid = :uuid");
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        if ($statement->rowCount() === 0) {
            $this->logger->warning("User not found for deletion", ['uuid' => (string)$uuid]);

            throw new UserNotFoundException("User not found: $uuid");
        }
    }
}