<?php

namespace ArtDevelopp\UserBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
#[Route(path: '/user/admin')]
class AdminManager extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }
    #[Route(path: '/manage', name: 'artdevelopp_user.admin-manage-user')]
    public function adminManageUser(Request $request)
    {
        if (!$this->isGranted($this->getParameter('user_bundle.role_admin'))) {
            throw $this->createNotFoundException("La ressource demandée n'existe pas");
        }

        $doctrine = $this->doctrine;
        $userRepository = $doctrine->getRepository($this->getParameter('user_bundle.user_class'));
        $users = $userRepository->findBy([], ['username' => 'ASC']);

        if ($request->isMethod('POST')) {

            //Récupération de l'utilisateur
            $userId = (int) $request->request->get('users');
            $user = $userRepository->findOneById($userId);

            if (!$user) {

                //Si l'utilisateur n'est pas correct −> retour sur la page avec erreur flashBag
                $this->addFlash('danger', "L'utilisateur n'existe pas");
                return $this->redirectToRoute('artdevelopp_user.admin-manage-user');
            }

            //Voir les infos user
            if ($request->request->get('viewUser')) {
                return $this->redirectToRoute('artdevelopp_user.manage-user', ['userId' => $userId]);
            }

            //Modification de l'utilisateur 
            if ($request->request->get('updateUser')) {
                return $this->redirectToRoute('artdevelopp_user.update-user', ['userId' => $userId]);
            }

            //Suppression de l'utilisateur 
            if ($request->request->get('deleteUser')) {
                return $this->redirectToRoute('artdevelopp_user.delete-user', ['userId' => $userId]);
            }
        }

        return $this->render('@ArtdeveloppUser/admin/manageUser.html.twig', [
            'users' => $users
        ]);
    }
}