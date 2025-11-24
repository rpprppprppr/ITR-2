<?php

namespace src\Blog\Repositories\UsersRepository;

use src\Blog\User;
use src\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}