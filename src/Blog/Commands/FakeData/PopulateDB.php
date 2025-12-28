<?php

namespace src\Blog\Commands\FakeData;

use Faker\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use src\Blog\UUID;
use src\Blog\User;
use src\Blog\Person\Name;
use src\Blog\Post;

class PopulateDB extends Command
{
    public function __construct(
        private Generator $faker,
        private UserRepositoryInterface $userRepository,
        private PostRepositoryInterface $postRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populate DB with fake data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = [];

        for ($i = 0; $i < 10; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln("User created: " . $user->getUsername());
        }

        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln("Post created: " . $post->getTitle());
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createForm(
            $this->faker->userName(),
            $this->faker->password(),
            new Name(
                $this->faker->firstName(),
                $this->faker->lastName(),
            ),
        );

        $this->userRepository->save($user);

        return $user;
    }

    private function createFakePost(User $user): Post
    {
        $post = new Post(
            UUID::random(),
            $user->getId(),
            $this->faker->title(),
            $this->faker->text(),
        );

        $this->postRepository->save($post);

        return $post;
    }
}