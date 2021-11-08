<?php

namespace ArtDevelopp\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/user")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class DeleteUser extends AbstractController
{

    /**
     * @Route("/delete/{userId}", name="artdevelopp_user.delete-user")
     */
    public function deleteUser(TokenStorageInterface $token, int $userId)
    {
        //suppression de l'utilisateur
        $user = $this->getUser();

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();

        if (!($this->isGranted($this->getParameter('user_bundle.role_admin')))) {
            if (!($user->getId() == $userId)) {
                throw $this->createNotFoundException("Impossible d'accéder à la ressource demandée");
            }
        }


        //Suppression si admin 
        if ($this->isGranted($this->getParameter('user_bundle.role_admin'))) {
            $user = $doctrine->getRepository($this->getParameter('user_bundle.user_class'))->findOneById($userId);
        }


        $entityManager->remove($user);
        $entityManager->flush();

        if ($this->isGranted($this->getParameter('user_bundle.role_admin'))) {
            $this->addFlash('success', "L'utilisateur à bien été supprimé !");
            return $this->redirectToRoute('user_bundle.admin-manage-user');
        }


        $token->setToken(null);

        $this->addFlash(
            'success',
            'Votre compte utilisateur a bien été supprimé !'
        );

        return $this->redirectToRoute('artdevelopp_user.login');
    }
}
