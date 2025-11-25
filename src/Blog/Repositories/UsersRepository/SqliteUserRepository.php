<?php

namespace src\Blog\Repositories\UsersRepository;

use PDO;
use PDOStatement;

use src\Blog\Person\Name;
use src\Blog\User;
use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\UUID;

readonly class SqliteUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    public function save(User $user): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO users (uuid, username, first_name, last_name) 
            VALUES (:uuid, :username, :first_name, :last_name)
        ");

        $statement->execute([
            ":uuid"=>$user->getId(),
            ":username"=>$user->getUsername(),
            ":first_name"=>$user->getName()->getFirstName(),
            ":last_name"=>$user->getName()->getLastName()
        ]);
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare("SELECT * FROM users WHERE uuid = :uuid");
        $statement->execute([
            ":uuid"=>$uuid,
        ]);

        return $this->getUser($statement, $uuid);
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare("SELECT * FROM users WHERE username = :username");
        $statement->execute([
            ":username"=>$username,
        ]);

        return $this->getUser($statement, $username);
    }

    public function getUser(PDOStatement $statement, string $userPayload): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new UserNotFoundException("User not found: $userPayload");
        }

        return new User(
            new UUID($result["uuid"]),
            $result["username"],
            new Name($result["first_name"], $result["last_name"])
        );
    }
}