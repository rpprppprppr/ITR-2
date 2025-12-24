<?php

namespace src\Blog\Http\Actions\Auth;

use DateTimeImmutable;

use src\Blog\AuthToken;
use src\Blog\Exceptions\AuthException;
use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\Auth\PasswordAuthentication;

use src\Blog\Http\ErrorResponse;
use src\Blog\Http\Request;
use src\Blog\Http\Response;
use src\Blog\Http\SuccessfulResponse;

use src\Blog\Repositories\AuthTokenRepository\AuthTokenRepositoryInterface;

readonly class Login implements ActionsInterface
{
    public function __construct(
        private PasswordAuthentication $passwordAuthentication,
        private AuthTokenRepositoryInterface $authTokenRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getId(),
            new DateTimeImmutable()->modify('+1 day')
        );

        $this->authTokenRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => (string)$authToken,
        ]);
    }
}