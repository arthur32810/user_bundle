<?php

namespace ArtDevelopp\UserBundle\Command;

use ArtDevelopp\UserBundle\Service\UserExistCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DeleteUserCommand extends Command 
{
    protected static $defaultName = "user_bundle:delete-user";
    protected static $defaultDescription = "Suppression d'un user";

    public function __construct(
        private UserExistCommand $userExistCommand,
        private EntityManagerInterface $manager,
        private ParameterBagInterface $params
    )
    {
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, "email de l'utilisateur")
            ->addArgument("username", InputArgument::REQUIRED, "nom d'utilisateur");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Récupération paramétre 
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');

        //Récupération de l'user 
        $user = $this->userExistCommand->userExist($email, $username, $output);

        if(!$user){
            return Command::FAILURE;
        }

        //Suppression de l'user 
        $this->manager->remove($user);
        $this->manager->flush();

        $output->writeln("L'utilisateur ".$username." à bien été supprimé !");

        return Command::SUCCESS;
    }
}
