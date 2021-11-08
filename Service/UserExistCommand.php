<?php 

namespace ArtDevelopp\UserBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserExistCommand
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private ParameterBagInterface $params)
    {
        
    }

    public function userExist($email, $username, $output)
    {
        //Récupération de l'utilisateur 
        $user = $this->manager->getRepository($this->params->get("user_bundle.user_class"))->findOneByEmail($email);

        //Test si l'utiliseteur exsite
        if (!$user) { 
            $output->writeln("L'email " . $email . " n'existe pas en base de donnée");
            return null;
        }

        //Test si username est correct
        if (!($user->getUsername() == $username)) {
            $output->writeln("Le nom d'utilisateur n'est pas correct");
            return null;
        }
        
        return $user;
    }
}
