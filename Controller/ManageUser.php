<?php

namespace ArtDevelopp\UserBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/manage")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class ManageUser extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }
    /**
     * @Route("/{userId}", name="artdevelopp_user.manage-user")
     */
    public function manageUser(int $userId)
    {
        //Récupération de l'user 
        $user = $this->getUser();

        if (!($this->isGranted($this->getParameter(('user_bundle.role_admin'))))) {
            if (!($user->getId() == $userId)) {
                throw $this->createNotFoundException("Impossible d'accéder à la ressource demandée !");
            }
        }

        //Récupération si admin 
        if ($this->isGranted($this->getParameter('user_bundle.role_admin'))) {
            $user = $this->doctrine->getRepository($this->getParameter('user_bundle.user_class'))->findOneById($userId);
        }

        return $this->render('@ArtdeveloppUser/manageUser/manageUser.html.twig', [
            'user' => $user,
        ]);
    }
}