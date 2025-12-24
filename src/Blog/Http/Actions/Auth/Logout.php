<?php

namespace src\Blog\Http\Actions\Auth;

use DateTimeImmutable;

use src\Blog\AuthToken;

use Exception;
use src\Blog\Exceptions\AuthException;

use src\Blog\Repositories\AuthTokenRepository\AuthTokenRepositoryInterface;

use src\Blog\Http\Actions\ActionsInterface;
use src\Blog\Http\Auth\TokenAuthenticationInterface;
use src\Blog\Http\Request;
use src\Blog\Http\Response;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\ErrorResponse;

readonly class Logout implements ActionsInterface
{
    public function __construct(
        private TokenAuthenticationInterface $authentication,
        private AuthTokenRepositoryInterface $authTokenRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);

            $header = $request->header('Authorization');
            if (!str_starts_with($header, 'Bearer ')) {
                throw new AuthException("Malformed token: [$header]");
            }
            $tokenString = mb_substr($header, strlen('Bearer '));

            $authToken = $this->authTokenRepository->get($tokenString);

            $expiredToken = new AuthToken(
                $authToken->getToken(),
                $user->getId(),
                new DateTimeImmutable()
            );

            $this->authTokenRepository->save($expiredToken);

            return new SuccessfulResponse([
                'message' => 'Logged out successfully'
            ]);
        } catch (AuthException $error) {
            return new ErrorResponse($error->getMessage());
        } catch (Exception $error) {
            return new ErrorResponse("Cannot logout: " . $error->getMessage());
        }
    }
}