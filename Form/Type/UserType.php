<?php

namespace ArtDevelopp\UserBundle\Form\Type;

//use ;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
{

    public function __construct(
        private ContainerBagInterface $params,
        private Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('email', EmailType::class)
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur '
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe']
            ]);
        if ($this->security->isGranted($this->params->get('user_bundle.role_admin'))) {
            $arrayRoles = [];

            $builder->add('roles', RolesUserType::class);
        }
        $builder->add('save', SubmitType::class, ['label' => "S'inscrire"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->params->get('user_bundle.user_class')
        ]);
    }
}
