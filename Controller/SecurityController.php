<?php

namespace ArtDevelopp\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name= "artdevelopp_user.login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        //last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('@ArtdeveloppUser/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'userIdentifier' => $this->getParameter('user_bundle.loginWith'),
            'user_register' => $this->getParameter('user_bundle.user_register'),
        ]);
    }
}
