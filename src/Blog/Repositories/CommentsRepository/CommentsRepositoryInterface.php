<?php

namespace src\Blog\Repositories\CommentsRepository;

use src\Blog\Comment;
use src\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
    public function getByPostId(UUID $uuid): Comment;
    public function getByAuthorId(UUID $uuid): Comment;
}