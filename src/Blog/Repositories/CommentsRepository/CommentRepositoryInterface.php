<?php

namespace src\Blog\Repositories\CommentsRepository;

use src\Blog\Comment;
use src\Blog\UUID;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}