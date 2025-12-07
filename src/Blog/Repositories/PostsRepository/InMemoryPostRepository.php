<?php

namespace src\Blog\Repositories\PostsRepository;

use src\Blog\UUID;
use src\Blog\Post;
use src\Blog\Exceptions\PostNotFoundException;

class InMemoryPostRepository implements PostRepositoryInterface
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

    public function delete(UUID $uuid): void
    {
        foreach ($this->posts as $index => $post) {
            if ((string)$post->getId() === (string)$uuid) {
                unset($this->posts[$index]);
                $this->posts = array_values($this->posts);
                return;
            }
        }

        throw new PostNotFoundException("Post not found: $uuid");
    }
}