<?php

namespace ArtDevelopp\UserBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class RolesService
{

    public function __construct(
        private ContainerBagInterface $params,
    ) {
    }

    public function getRolesApp()
    {
        $arrayRoles = [];

        //Récupération des rôles
        foreach ($this->params->get('security.role_hierarchy.roles') as $keyRole => $role) {
            $arrayRoles += [$keyRole => $keyRole];
        }

        return $arrayRoles;
    }
}
