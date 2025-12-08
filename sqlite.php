<?php

$connection = new PDO("sqlite:" . __DIR__ . "/blog.sqlite");

$connection->exec("DROP TABLE IF EXISTS comments;");
$connection->exec("DROP TABLE IF EXISTS posts;");
$connection->exec("DROP TABLE IF EXISTS users;");
$connection->exec("DROP TABLE IF EXISTS postLikes;");
$connection->exec("DROP TABLE IF EXISTS commentLikes;");

$connection->exec("
    CREATE TABLE users (
        uuid TEXT NOT NULL PRIMARY KEY,
        username TEXT NOT NULL UNIQUE,
        first_name TEXT,
        last_name TEXT
    );
");

$connection->exec("
    CREATE TABLE posts(
          uuid TEXT NOT NULL CONSTRAINT posts_uuid_primary_key PRIMARY KEY,
          author_uuid TEXT NOT NULL,
          title TEXT NOT NULL,
          text TEXT NOT NULL,
          FOREIGN KEY (author_uuid) REFERENCES users(uuid)
    );
");

$connection->exec("
    CREATE TABLE comments(
         uuid TEXT NOT NULL CONSTRAINT comments_uuid_primary_key PRIMARY KEY,
         post_uuid TEXT NOT NULL,
         author_uuid TEXT NOT NULL,
         text TEXT NOT NULL,
         FOREIGN KEY (post_uuid) REFERENCES posts(uuid),
         FOREIGN KEY (author_uuid) REFERENCES users(uuid)
    );
");

$connection->exec("
    CREATE TABLE postLikes(
         uuid TEXT NOT NULL CONSTRAINT postLikes_uuid_primary_key PRIMARY KEY,
         post_uuid TEXT NOT NULL,
         user_uuid TEXT NOT NULL,
         FOREIGN KEY (post_uuid) REFERENCES posts(uuid),
         FOREIGN KEY (user_uuid) REFERENCES users(uuid)
    );
");

$connection->exec("
    CREATE UNIQUE INDEX unique_like
    ON postLikes(post_uuid, user_uuid);
");

$connection->exec("
    CREATE TABLE commentLikes(
         uuid TEXT NOT NULL CONSTRAINT commentLikes_uuid_primary_key PRIMARY KEY,
         comment_uuid TEXT NOT NULL,
         user_uuid TEXT NOT NULL,
         FOREIGN KEY (comment_uuid) REFERENCES comments(uuid),
         FOREIGN KEY (user_uuid) REFERENCES users(uuid)
    );
");

$connection->exec("
    CREATE UNIQUE INDEX unique_like 
    ON commentLikes(comment_uuid, user_uuid);
");