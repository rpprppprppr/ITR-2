<?php

namespace src\Blog\Repositories\PostsRepository;

use src\Blog\UUID;
use src\Blog\Post;
use src\Blog\Exceptions\PostNotFoundException;

class InMemoryPostsRepository implements PostsRepositoryInterface
{
    private array $posts = [];

    public function save(Post $post): void
    {
        $this->posts[] = $post;
    }

    public function get(UUID $uuid): Post
    {
        foreach ($this->posts as $post) {
            if ((string)$post->getId() === (string)$uuid) {
                return $post;
            }
        }

        throw new PostNotFoundException("Post not found: $uuid");
    }

    public function getByAuthorId(UUID $uuid): Post
    {
        foreach ($this->posts as $post) {
            if ((string)$post->getAuthorId() === (string)$uuid) {
                return $post;
            }
        }

        throw new PostNotFoundException("Post not found: $uuid (authorId)");
    }
}