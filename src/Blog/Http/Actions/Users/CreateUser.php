<?php

namespace src\Blog\Http\Actions\Users;

use src\Blog\UUID;
use src\Blog\User;
use src\Blog\Person\Name;

use src\Blog\Exceptions\HttpException;

use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;

use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

readonly class CreateUser implements ActionsInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();
            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('username'),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                )
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->userRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid
        ]);
    }
}