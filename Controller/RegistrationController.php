<?php

namespace ArtDevelopp\UserBundle\Controller;

use ArtDevelopp\UserBundle\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/new", name="artdevelopp_user.new")
     */
    public function registrationUser(Request $request, UserPasswordHasherInterface $passwordHasher, \Swift_Mailer $mailer): Response
    {
        if (!($this->getParameter('user_bundle.user_register')) && !($this->isGranted($this->getParameter('user_bundle.role_admin')))) {
            throw $this->createNotFoundException('La fonctionnalité d\'enregistrement n\'est pas ouverte.');
        }

        //Création d'un nouveau form user
        $entityUser = $this->getParameter('user_bundle.user_class');
        $user = new $entityUser();
        $form = $this->createForm(UserType::class, $user);

        // 2) gestion de l'envoie
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Encodage du mot de passe
            $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            if ($this->getParameter('user_bundle.confirm_email')) {
                // Génération d'un token si confirmation email utilisé
                $user->setUserActivated(false);
                $user->setActivationToken(md5(uniqid()));

                $message = (new \Swift_Message('Nouveau compte'))
                    ->setFrom($this->getParameter('user_bundle.mail_sender_address'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            '@artdevelopp_user_bundle/emails/activation.html.twig',
                            ['token' => $user->getActivationToken()]
                        ),
                        'text/html'
                    );

                $mailer->send($message);
            } else {
                $user->setUserActivated(true);
            }


            //4) Save the user 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            if ($this->getParameter('user_bundle.confirm_email')) {
                $this->addFlash('success', 'Veuillez activer votre compte, via le lien reçu par mail avant de vous connecter');
            } else {
                $this->addFlash(
                    'success',
                    'Utilisateur créé, vous pouvez vous connecter dès maintenant'
                );
            }

            return $this->redirectToRoute('artdevelopp_user.login');
        }


        return $this->renderForm('@artdevelopp_user_bundle/registration/register.html.twig',  [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/activation/{token}", name="artdevelopp_user.activation")
     */
    public function activation($token)
    {
        //Récupération doctrine
        $entityManager = $this->getDoctrine()->getManager();

        //Recherche si user avec token existe
        $user = $entityManager->getRepository($this->getParameter('user_bundle.user_class'))->findOneBy(['activation_token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        //on supprimer le token
        $user->setUserActivated(true);
        $user->setActivationToken(null);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur activé avec succès');

        //retour page connexion 
        return $this->redirectToRoute('artdevelopp_user_login');
    }
}
