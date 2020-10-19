<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;

class RemoveInactiveCommand extends Command
{
    protected static $defaultName = 'remove-inactive';
    private $userRepo;
    private $em;

    public function __construct(UsersRepository $userRepo, EntityManagerInterface $em)
    {
        $this->userRepo = $userRepo;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
            $this->setDescription('Delete inactive users after 24h')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inactiveUsers = $this->userRepo->findBy(['active' => 0]);
        $count = count($inactiveUsers);
        foreach($inactiveUsers as $inactiveUser) {
            $this->em->remove($inactiveUser);
        }
        $this->em->flush();
        $io = new SymfonyStyle($input, $output);

        $io->success("$count users have been deleted");

        return 0;
    }
}
