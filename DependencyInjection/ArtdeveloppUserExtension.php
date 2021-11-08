<?php

namespace ArtDevelopp\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ArtdeveloppUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('services.yaml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('user_bundle.user_register', $config['user_register']);
        $container->setParameter('user_bundle.loginWith', $config['loginWith']);
        $container->setParameter('user_bundle.user_class', $config['user_class']);
        $container->setParameter('user_bundle.confirm_email', $config['confirm_email']);
        $container->setParameter('user_bundle.mail_sender_address', $config['mail_sender_address']);
        $container->setParameter('user_bundle.role_admin', $config['role_admin']);
        $container->setParameter('user_bundle.reset_role', $config['reset_role']);
        $container->setParameter('user_bundle.default_role', $config['default_role']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $twigConfig = [];
        $twigConfig['paths'][__DIR__ . '/../Resources/views'] = "artdevelopp_user_bundle";
        $container->prependExtensionConfig('twig', $twigConfig);
    }

    public function getAlias()
    {
        return parent::getAlias();
    }
}
