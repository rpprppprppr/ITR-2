<?php

require_once __DIR__ . "/vendor/autoload.php";

use src\Blog\User;
use src\Blog\Article;
use src\Blog\Comment;

$user1 = new User(0, "Василий", "Петрович");
$user2 = new User(1, "Петр", "Иннокентьевич");

$article = new Article(
    0,
    $user1->getId(),
    "Hello World!", "Так начинается первая программа на любом языке программирования");

$comment = new Comment(
    0,
    $user2->getId(),
0, "Это очень интересный факт!");

print "Пользователь 1: " . $user1->getFullName() . " (ID: " . $user1->getId() . ")<br/>";
print "Пользователь 2: " . $user2->getFullName() . " (ID: " . $user2->getId() . ")<br/>";
print "Статья: " . $article->getTitle() . "<br/>";
print "Текст статьи: " . $article->getText() . "<br/>";
print "Комментарий: " . $comment->getText() . " От пользователя (ID) - " . $comment->GetAuthorId() . "<br/>";