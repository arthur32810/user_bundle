<?php

namespace ArtDevelopp\UserBundle\Command;

use ArtDevelopp\UserBundle\Service\UserExistCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordCommand extends Command
{
    protected static $defaultName = "user_bundle:reset-password";
    protected static $defaultDescription = "Modification du mot de passe utilisateur";

    public function __construct(
        private UserExistCommand $userExistCommand,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $manager,
        private ParameterBagInterface $params,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, "email de l'utilisateur")
            ->addArgument('username', InputArgument::REQUIRED, "nom d'utilisateur")
            ->addArgument("password", InputArgument::REQUIRED, "nouveau mot de passe")
            ->addArgument("reset_role", InputArgument::OPTIONAL, "réinitialiser les roles de l'utilisateur");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        //Récupération des paramétres
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $reset_role = (bool) $input->getArgument('reset_role');

        //Récupération de l'user
        $user = $this->userExistCommand->UserExist($email, $username, $output);

        if (!$user) {
            return Command::FAILURE;
        }
        //Modification du mot de passe
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        //Si reset role on remet le role par defaut 
        if ($this->params->get('user_bundle.reset_role')) {
            $user->setRoles([$this->params->get('user_bundle.default_role')]);
        }

        $this->manager->persist($user);
        $this->manager->flush();


        $output->writeln("Le mot de passe utilisateur a bien été changé !");

        if ($this->params->get("user_bundle.reset_role")) {
            $output->writeln("Les roles de l'utilisateur ont été remis par défaut");
        }

        return Command::SUCCESS;
    }
}
