<?php

namespace src\Blog\Http\Auth;

use DateTimeImmutable;
use src\Blog\Exceptions\AuthException;
use src\Blog\Exceptions\AuthTokenRepositoryException;
use src\Blog\Exceptions\HttpException;
use src\Blog\Http\Request;
use src\Blog\Repositories\AuthTokenRepository\AuthTokenRepositoryInterface;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\User;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokenRepositoryInterface $authTokenRepository,
        private UserRepositoryInterface $userRepository,
    )
    {}

    public function user(Request $request): User
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $error) {
            throw new AuthException($error->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try {
            $authToken = $this->authTokenRepository->get($token);
        } catch (AuthTokenRepositoryException) {
            throw new AuthException("Bad token: [$token]");
        }

        if ($authToken->getExpiresOn() < new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        $userUuid = $authToken->getUserUuid();

        return $this->userRepository->get($userUuid);
    }
}