<?php

namespace src\Blog\Commands\Users;

use src\Blog\Person\Name;
use src\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use src\Blog\User;
use src\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Update a user')
            ->addArgument('uuid', InputArgument::REQUIRED, 'UUID of a user to update')
            ->addOption('first-name', 'f', InputArgument::OPTIONAL, 'First name')
            ->addOption('last-name', 'l', InputArgument::OPTIONAL, 'Last name');
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        if (empty($firstName) && empty($lastName)) {
            $output->writeln("Nothing to update");
            return Command::SUCCESS;
        }

        $uuid = $input->getArgument('uuid');
        $user = $this->userRepository->get(new UUID($uuid));
        $updateName = new Name(
            empty($firstName) ? $user->getName()->getFirstName() : $firstName,
            empty($lastName) ? $user->getName()->getLastName() : $lastName,
        );

        $updateUser = new User(
            new UUID($uuid),
            $user->getUsername(),
            $user->getHashedPassword(),
            $updateName
        );

        $this->userRepository->save($updateUser);

        $output->writeln("User update: " . $user->getId());

        return Command::SUCCESS;
    }
}