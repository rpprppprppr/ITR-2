<?php

namespace src\Blog\Commands;

use Psr\Log\LoggerInterface;
use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\Exceptions\CommandException;
use src\Blog\UUID;
use src\Blog\User;
use src\Blog\Person\Name;

readonly class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    )
    {}

    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExist($username)) {
            $this->logger->warning("User already exists: $username");
            throw new CommandException(
                "User already exists: $username"
            );
        }

        $uuid = UUID::random();

        $this->usersRepository->save(new User(
            $uuid,
            $username,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name'),
            )
        ));

        $this->logger->info("User created: $uuid");
    }

    public function userExist(string $username): bool
    {
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }
}