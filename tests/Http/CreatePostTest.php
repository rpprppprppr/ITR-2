<?php

namespace src\Blog\UnitTests\Http;

use PDO;
use PHPUnit\Framework\TestCase;

use src\Blog\UUID;
use src\Blog\User;
use src\Blog\Person\Name;


use src\Blog\Repositories\PostsRepository\SqlitePostRepository;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\UnitTests\DummyLogger;

use src\Blog\Http\Request;
use src\Blog\Http\Actions\Posts\CreatePost;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\ErrorResponse;

use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Exceptions\HttpException;

class CreatePostTest extends TestCase
{
    private PDO $connection;
    private SqlitePostRepository $postRepository;
    private UserRepositoryInterface $userRepositoryStub;

    protected function setUp(): void
    {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connection->exec("
            CREATE TABLE posts (
                uuid TEXT,
                author_uuid TEXT,
                title TEXT,
                text TEXT
            )
        ");

        $this->postRepository = new SqlitePostRepository($this->connection, new DummyLogger());
        $this->userRepositoryStub = $this->createStub(UserRepositoryInterface::class);
    }

    public function testSuccessfulResponse(): void
    {
        $authorUuid = UUID::random();

        $this->userRepositoryStub->method('get')->willReturn(
            new User($authorUuid, 'username', new Name('First', 'Last'))
        );

        $body = json_encode([
            'author_uuid' => (string)$authorUuid,
            'title' => 'Test Title',
            'text' => 'Test Text'
        ]);

        $request = new Request([], [], $body);
        $action = new CreatePost($this->postRepository, $this->userRepositoryStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $payload = $response->payloadData();
        $this->assertArrayHasKey('uuid', $payload);

        $statement = $this->connection->prepare("SELECT * FROM posts WHERE uuid = :uuid");
        $statement->execute([":uuid" => $payload['uuid']]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($row);
        $this->assertSame('Test Title', $row['title']);
        $this->assertSame('Test Text', $row['text']);
        $this->assertSame((string)$authorUuid, $row['author_uuid']);
    }

    public function testInvalidUuidReturnsError(): void
    {
        $this->userRepositoryStub->method('get')->willThrowException(new HttpException("Invalid UUID"));

        $body = json_encode([
            'author_uuid' => 'invalid-uuid',
            'title' => 'Title',
            'text' => 'Text'
        ]);

        $request = new Request([], [], $body);
        $action = new CreatePost($this->postRepository, $this->userRepositoryStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertStringContainsString('Malformed UUID', $response->reason());
    }

    public function testUserNotFoundReturnsError(): void
    {
        $this->userRepositoryStub->method('get')->willThrowException(new UserNotFoundException("User not found"));

        $body = json_encode([
            'author_uuid' => (string)UUID::random(),
            'title' => 'Title',
            'text' => 'Text'
        ]);

        $request = new Request([], [], $body);
        $action = new CreatePost($this->postRepository, $this->userRepositoryStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertStringContainsString('Author not found', $response->reason());
    }

    public function testMissingFieldsReturnsError(): void
    {
        $this->userRepositoryStub->method('get')->willReturn(
            new User(UUID::random(), 'username', new Name('First', 'Last'))
        );

        $body = json_encode([
            'author_uuid' => (string)UUID::random(),
            'text' => 'Text'
        ]);

        $request = new Request([], [], $body);
        $action = new CreatePost($this->postRepository, $this->userRepositoryStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertStringContainsString('No such field', $response->reason());
    }
}