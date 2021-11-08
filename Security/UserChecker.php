<?php

namespace ArtDevelopp\UserBundle\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if(!$user->getUserActivated()){
            throw new CustomUserMessageAccountStatusException('Votre compte utilisateur n\'est pas actif !') ;
        }
        
    }

    public function checkPostAuth(UserInterface $user)
    {
        
    }
}