<?php

namespace src\Blog\Repositories\AuthTokenRepository;

use src\Blog\AuthToken;

interface AuthTokenRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}