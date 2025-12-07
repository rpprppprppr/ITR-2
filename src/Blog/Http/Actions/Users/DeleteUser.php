<?php

namespace src\Blog\Http\Actions\Users;

use src\Blog\UUID;

use src\Blog\Exceptions\HttpException;
use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Exceptions\InvalidArgumentException;

use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\ErrorResponse;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;

class DeleteUser implements ActionsInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->userRepository->delete($uuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse("User not found: " . $e->getMessage());
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        } catch (InvalidArgumentException $e) {
            return new ErrorResponse("Invalid UUID");
        }

        return new SuccessfulResponse([
            'message' => 'User deleted successfully',
            'uuid' => (string)$uuid
        ]);
    }
}