<?php

namespace ArtDevelopp\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('user_bundle');

        $rootNode = $builder->getRootNode();
        $rootNode->children()
            ->booleanNode('user_register')
            ->defaultValue(true)
            ->end()
            ->scalarNode('loginWith')
            ->defaultValue("email")
            ->end()
            ->scalarNode('user_class')
            ->defaultValue("App\Entity\User")
            ->end()
            ->booleanNode('confirm_email')
            ->defaultValue(true)
            ->end()
            ->scalarNode('mail_sender_address')
            ->isRequired()
            ->end()
            ->scalarNode('role_admin')
            ->defaultValue('ROLE_ADMIN')
            ->end()
            ->booleanNode('reset_role')
            ->defaultValue(false)
            ->end()
            ->scalarNode('default_role')
            ->defaultValue('ROLE_USER')
            ->end()
            ->end();

        return $builder;
    }
}
