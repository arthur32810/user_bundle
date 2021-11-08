<?php

namespace ArtDevelopp\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/manage")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class ManageUser extends AbstractController
{
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

        $doctrine = $this->getDoctrine();
        //Récupération si admin 
        if ($this->isGranted($this->getParameter('user_bundle.role_admin'))) {
            $user = $doctrine->getRepository($this->getParameter('user_bundle.user_class'))->findOneById($userId);
        }

        return $this->render('@artdevelopp_user_bundle/manageUser/manageUser.html.twig', [
            'user' => $user,
        ]);
    }
}
