<?php

namespace src\Blog\Commands;

use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\Exceptions\CommandException;
use src\Blog\UUID;
use src\Blog\User;
use src\Blog\Person\Name;

class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {}

    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExist($username)) {
            throw new CommandException(
                "User already exists: $username"
            );
        }

        $this->usersRepository->save(new User(
            UUID::random(),
            $username,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name'),
            )
        ));
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