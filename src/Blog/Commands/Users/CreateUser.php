<?php

namespace src\Blog\Commands\Users;

use src\Blog\Exceptions\UserNotFoundException;
use src\Blog\Person\Name;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface $usersRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:create')
            ->setDescription('Creates new user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
            ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $output->writeln('Create user command started');
        $username = $input->getArgument('username');

        if ($this->userExist($username)) {
            $output->writeln("User already exists: $username");
            return Command::FAILURE;
        }

        $user = User::createForm(
            $username,
            $input->getArgument('password'),
            new Name(
                $input->getArgument('first_name'),
                $input->getArgument('last_name')
            )
        );

        $this->usersRepository->save($user);

        $output->writeln("User created: " . $user->getId());
        return Command::SUCCESS;
    }

    public function userExist(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }
}