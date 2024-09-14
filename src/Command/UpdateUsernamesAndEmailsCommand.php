<?php

// src/Command/UpdateUsernamesAndEmailsCommand.php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateUsernamesAndEmailsCommand extends Command
{
    protected static $defaultName = 'app:update-usernames-emails';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Update usernames and emails to remove spaces');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $username = str_replace(' ', '', $user->getUsername());
            $email = str_replace(' ', '', $user->getEmail());

            $user->setUsername($username);
            $user->setEmail($email);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $io->success('Usernames and emails have been updated successfully.');

        return Command::SUCCESS;
    }
}
