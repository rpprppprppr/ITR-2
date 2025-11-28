<?php

$connection = new PDO("sqlite:" . __DIR__ . "/test.sqlite");

$sql = <<<SQL
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS comments;

CREATE TABLE users(
      uuid TEXT NOT NULL CONSTRAINT users_uuid_primary_key PRIMARY KEY,
      username TEXT NOT NULL CONSTRAINT username_unique UNIQUE,
      first_name TEXT,
      last_name TEXT
);

CREATE TABLE posts(
      uuid TEXT NOT NULL CONSTRAINT posts_uuid_primary_key PRIMARY KEY,
      author_uuid TEXT NOT NULL,
      title TEXT NOT NULL,
      text TEXT NOT NULL,
      FOREIGN KEY (author_uuid) REFERENCES users(uuid)
);

CREATE TABLE comments(
     uuid TEXT NOT NULL CONSTRAINT comments_uuid_primary_key PRIMARY KEY,
     post_uuid TEXT NOT NULL,
     author_uuid TEXT NOT NULL,
     text TEXT NOT NULL,
     FOREIGN KEY (post_uuid) REFERENCES posts(uuid),
     FOREIGN KEY (author_uuid) REFERENCES users(uuid)
);
SQL;

$connection->exec($sql);