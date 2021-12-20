<?php

namespace ArtDevelopp\UserBundle\Form\Type;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesUserType extends AbstractType
{
    public function __construct(
        private ContainerBagInterface $params,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //Récupération des rôles
        foreach ($this->params->get('security.role_hierarchy.roles') as $keyRole => $role) {
            $arrayRoles = [];
            $arrayRoles += [$keyRole => $keyRole];
        }

        $resolver->setDefaults([
            'choices' => $arrayRoles,
            'multiple' => true
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
