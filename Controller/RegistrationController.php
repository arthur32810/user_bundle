<?php

namespace ArtDevelopp\UserBundle\Controller;

use ArtDevelopp\UserBundle\Form\Type\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class RegistrationController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }
    /**
     * @Route("/new", name="artdevelopp_user.new")
     */
    public function registrationUser(Request $request, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        //Si l'enregistrement de nouvel user n'est pas permis alors on met une erreur sauf si user admin
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

            //enregsitrement de la date d'enregistrement
            $user->setRegistrationDate(new \DateTime());

            //Génération token si confirmation user par mail
            if ($this->getParameter('user_bundle.confirm_email')) {

                // Génération d'un token si confirmation email utilisé
                $user->setUserActivated(false);
                $user->setActivationToken(md5(uniqid()));

                $userToken = $user->getActivationToken();
            } else {
                $user->setUserActivated(true);
            }

            //4) Save the user 
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            //Envoi mail si token user 
            if ($userToken) {
                $email = (new TemplatedEmail())
                    ->from($this->getParameter('user_bundle.mail_sender_address'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de la création de votre compte')
                    ->htmlTemplate('@ArtdeveloppUser/emails/activation.html.twig')
                    ->context([
                        'token' => $user->getActivationToken()
                    ]);


                $mailer->send($email);
            }

            if ($this->isGranted($this->getParameter('user_bundle.role_admin'))) {

                if ($this->getParameter('user_bundle.confirm_email')) {
                    $this->addFlash('success', "L'utilisateur a été créé, il doit maintenant activer son compte via le lien reçu par mail");
                } else {
                    $this->addFlash(
                        'success',
                        'L\'Utilisateur a bien été créé, il peut se connecter dès maintenant'
                    );
                }
            } else {
                if ($this->getParameter('user_bundle.confirm_email')) {
                    $this->addFlash('success', 'Veuillez activer votre compte, via le lien reçu par mail avant de vous connecter');
                } else {
                    $this->addFlash(
                        'success',
                        'Utilisateur créé, vous pouvez vous connecter dès maintenant'
                    );
                }
            }

            //Redirection vers page d'accueil ou menu user si administrateur
            if ($this->isGranted($this->getParameter('user_bundle.role_admin'))) {
                return $this->redirectToRoute('artdevelopp_user.admin-manage-user');
            } else {
                return $this->redirectToRoute('artdevelopp_user.login');
            }
        }


        return $this->renderForm('@ArtdeveloppUser/registration/register.html.twig',  [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/activation/{token}", name="artdevelopp_user.activation")
     */
    public function activation($token)
    {
        //Récupération doctrine
        $entityManager = $this->doctrine->getManager();

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
        return $this->redirectToRoute('artdevelopp_user.login');
    }
}