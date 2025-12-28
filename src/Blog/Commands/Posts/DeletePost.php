<?php

namespace src\Blog\Commands\Posts;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use src\Blog\Exceptions\PostNotFoundException;
use src\Blog\Repositories\PostsRepository\PostRepositoryInterface;

use src\Blog\UUID;

class DeletePost extends Command
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Delete a post')
            ->addArgument('uuid', InputArgument::REQUIRED, ' of a post to delete')
            ->addOption('check-existence', 'c', InputOption::VALUE_NONE, 'Check if post actually exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $question = new ConfirmationQuestion(
            'Delete post [Y/n]?',
            false
        );

        if (!$this->getHelper('question')->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $uuid = new UUID($input->getArgument('uuid'));

        if ($input->getOption('check-existence')) {
            try {
                $this->postRepository->get($uuid);
            } catch (PostNotFoundException $error) {
                $output->writeln($error->getMessage());
                return Command::FAILURE;
            }
        }

        $this->postRepository->delete($uuid);

        $output->writeln("Post $uuid was deleted");

        return Command::SUCCESS;
    }
}