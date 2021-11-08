<?php

namespace ArtDevelopp\UserBundle\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationUserCommand extends Command
{
    protected static $defaultName = "user_bundle:add-user";
    protected static $defaultDescription = "Cette commande permet d\'ajouter un utilisateur";

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $manager,
        private ParameterBagInterface $params,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'email de l\'utilisateur')
            ->addArgument('username', InputArgument::REQUIRED, 'nom d\'utilisateur')
            ->addArgument('password', InputArgument::REQUIRED, 'mot de passe')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role à definir, le role sera ajouté de cette façon ROLE_[votreRole]');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Récupération paramétre
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        if ($input->getArgument('role')) {
            $role = ['ROLE_' . $input->getArgument('role')];
        } else {
            $role = [$this->params->get("user_bundle.default_role")];
        }

        //Création User 
        $user = new ($this->params->get('user_bundle.user_class'));
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles($role);
        $user->setUserActivated(true);

        $this->manager->persist($user);
        $this->manager->flush();

        $output->writeln('L\'utilisateur ' . $username . ' a bien été créé !');

        return Command::SUCCESS;
    }
}
