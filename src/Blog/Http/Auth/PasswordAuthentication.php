<?php

namespace src\Blog\Http\Auth;

use src\Blog\User;
use src\Blog\Http\Request;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Exceptions\AuthException;
use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\UserNotFoundException;

readonly class PasswordAuthentication implements AuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {}

    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField("username");
        } catch (HttpException $error) {
            throw new AuthException($error->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $error) {
            throw new AuthException($error->getMessage());
        }

        try {
            $password = $request->jsonBodyField("password");
        } catch (HttpException $error) {
            throw new AuthException($error->getMessage());
        }

        if ($user->checkPassword($password)) {
            throw new AuthException("Invalid password");
        }

        return $user;
    }
}