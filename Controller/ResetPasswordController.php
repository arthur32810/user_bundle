<?php

namespace ArtDevelopp\UserBundle\Controller;

use ArtDevelopp\UserBundle\Form\Type\ResetPassType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * @Route("/user")
 */
class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/mot-de-passe-oublie", name="artdevelopp_user.forget_password")
     */
    public function forgetPassword(Request $request, TokenGeneratorInterface $tokenGenerator, \Swift_Mailer $mailer)
    {
        //initialisation formulaire
        $form = $this->createForm(ResetPassType::class);

        //on trait le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on récupére les données
            $donnees = $form->getData();

            //On cherche si un utilisateur existe avec l'email 
            $user = $this->getDoctrine()->getRepository($this->getParameter('user_bundle.user_class'))->findOneByEmail($donnees['email']);

            if ($user == null) {
                //erreur adresse mail inconnu
                $this->addFlash('danger', 'Cette adresse email est inconnue');

                return $this->redirectToRoute('artdevelopp_user.login');
            }

            //On génére un token 
            $token = $tokenGenerator->generateToken();

            //On essaie d'envoyer le token en base de donnée
            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('artdevelopp_user_login');
            }

            //On génére l'url de réinitialisation mot de passe
            $url = $this->generateUrl('artdevelopp_user.reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            //Génération email 
            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom($this->getParameter('user_bundle.mail_sender_address'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        '@ArtdeveloppUser/emails/resetPassword.html.twig',
                        ['urlResetPassword' => $url]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success', 'Un mail contenant un lien de réinitalisation de votre mot de passe vous a été envoyé !');

            return $this->redirectToRoute('artdevelopp_user.login');
        }

        //On envoie le formulaire à la vue 
        return $this->renderForm('@ArtdeveloppUser/resetPassword/forget_password.html.twig', ['emailForm' => $form]);
    }

    /**
     * @Route("/reinitialisation-mot-de-passe/{token}", name="artdevelopp_user.reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordHasherInterface $userPasswordHasher)
    {

        //on cherche l'utilisateur via le token 
        $user = $this->getDoctrine()->getRepository($this->getParameter('user_bundle.user_class'))->findOneBy(['reset_token' => $token]);

        if ($user == null) {
            $this->addFlash('danger', 'Token iconnu');
            return $this->redirectToRoute('artdevelopp_user.login');
        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($userPasswordHasher->hashPassword($user, $request->request->get('password')));

            //reset des role si activé 
            if ($this->getParameter('user_bundle.reset_role')) {
                $user->setRoles([$this->getParameter('user_bundle.default_role')]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Mot de passe mis à jour'
            );

            return $this->redirectToRoute('artdevelopp_user.login');
        }

        return $this->render('@ArtdeveloppUser/resetPassword/reset_password.html.twig', ['token' => $token]);
    }
}
