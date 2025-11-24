<?php

namespace src\Blog\Repositories\PostsRepository;

use src\Blog\Post;
use src\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function getByAuthorId(UUID $uuid): Post;
}