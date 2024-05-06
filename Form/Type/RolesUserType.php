<?php

namespace ArtDevelopp\UserBundle\Form\Type;

use ArtDevelopp\UserBundle\Service\RolesService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesUserType extends AbstractType
{
    public function __construct(
        private RolesService $rolesService
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'choices' => $this->rolesService->getRolesApp(),
            'multiple' => true
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
