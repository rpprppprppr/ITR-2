<?php

namespace src\Blog\Http\Actions\Users;

use src\Blog\Exceptions\HttpException;

use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;

use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class FindByUsername implements ActionsInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $user = $this->userRepository->getByUsername($username);
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'username' => $username,
            'name' => (string)$user->getName()
        ]);
    }
}