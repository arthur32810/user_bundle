<?php

namespace ArtDevelopp\UserBundle\Controller;

use ArtDevelopp\UserBundle\Form\Type\UserUpdateType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class UpdateUser extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine)
    {
    }
    /**
     * @Route("/update/{userId}", name="artdevelopp_user.update-user")
     */
    public function updateUser(Request $request, int $userId, UserPasswordHasherInterface $userPasswordHasher)
    {
        //Récupération de l'user
        $user = $this->getUser();

        if (!($this->isGranted($this->getParameter(('user_bundle.role_admin'))))) {
            if (!($user->getId() == $userId)) {
                throw $this->createNotFoundException("Impossible d'accéder à la ressource demandée !");
            }
        }

        $doctrine = $this->doctrine;
        //Modification si admin 
        if ($this->isGranted($this->getParameter('user_bundle.role_admin'))) {
            $user = $doctrine->getRepository($this->getParameter('user_bundle.user_class'))->findOneById($userId);
        }

        //Création du formulaire
        $entityUser = $this->getParameter('user_bundle.user_class');
        $form = $this->createForm(UserUpdateType::class, $user);

        //gestion de l'envoie
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Si modification mot de passe 
            $plainPassword = $form->getData()->getPlainPassword();
            if ($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }


            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vous avez bien modifié votre compte utilisateur'
            );

            return $this->redirectToRoute('artdevelopp_user.manage-user', ['userId' => $user->getId()]);
        }

        return $this->renderForm('@ArtdeveloppUser/updateUser/updateUser.html.twig', ['form' => $form, 'userId' => $userId]);
    }
}