<?php

namespace src\Blog\UnitTests\Http;

use PDO;
use PHPUnit\Framework\TestCase;

use src\Blog\Http\Auth\TokenAuthenticationInterface;
use src\Blog\UUID;
use src\Blog\User;
use src\Blog\Person\Name;

use src\Blog\Repositories\PostsRepository\SqlitePostRepository;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\Http\Auth\AuthenticationInterface;

use src\Blog\UnitTests\DummyLogger;

use src\Blog\Http\Request;
use src\Blog\Http\Actions\Posts\CreatePost;
use src\Blog\Http\SuccessfulResponse;
use src\Blog\Http\ErrorResponse;

use src\Blog\Exceptions\AuthException;

class CreatePostTest extends TestCase
{
    private PDO $connection;
    private SqlitePostRepository $postRepository;
    private TokenAuthenticationInterface $authenticationStub;

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
        $this->authenticationStub = $this->createStub(TokenAuthenticationInterface::class);
    }

    public function testSuccessfulResponse(): void
    {
        $authorUuid = UUID::random();
        $this->authenticationStub->method('user')->willReturn(
            new User(
                $authorUuid,
                'username',
                '123',
                new Name('First', 'Last')
            )
        );

        $body = json_encode([
            'title' => 'Test Title',
            'text' => 'Test Text'
        ]);

        $request = new Request([], [], $body);
        $action = new CreatePost($this->postRepository, $this->authenticationStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $payload = $response->payloadData();
        $this->assertArrayHasKey('uuid', $payload);

        $statement = $this->connection->prepare("SELECT * FROM posts WHERE uuid = :uuid");
        $statement->execute([':uuid' => $payload['uuid']]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($row);
        $this->assertSame('Test Title', $row['title']);
        $this->assertSame('Test Text', $row['text']);
        $this->assertSame((string)$authorUuid, $row['author_uuid']);
    }

    public function testAuthErrorReturnsError(): void
    {
        $this->authenticationStub
            ->method('user')
            ->willThrowException(new AuthException('Invalid password'));

        $body = json_encode([
            'title' => 'Title',
            'text' => 'Text'
        ]);

        $request = new Request([], [], $body);
        $action = new CreatePost($this->postRepository, $this->authenticationStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertStringContainsString(
            'Invalid password',
            $response->reason()
        );
    }

    public function testMissingFieldsReturnsError(): void
    {
        $authorUuid = UUID::random();
        $this->authenticationStub->method('user')->willReturn(
            new User(
                $authorUuid,
                'username',
                '123',
                new Name('First', 'Last')
            )
        );

        $body = json_encode([
            'text' => 'Text'
        ]);

        $request = new Request([], [], $body);
        $action = new CreatePost($this->postRepository, $this->authenticationStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertStringContainsString('No such field', $response->reason());
    }
}