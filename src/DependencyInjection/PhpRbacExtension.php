<?php

namespace RbacBundle\DependencyInjection;

use RbacBundle\Core\RbacInterface;
use Symfony\Component\Config\FileLocator;
use RbacBundle\Core\RoleManagerInterface;
use RbacBundle\Core\PermissionManagerInterface;
use RbacBundle\Entity\PermissionInterface;
use RbacBundle\Entity\RoleInterface;
use RbacBundle\EventSubscriber\AccessControlDriver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PhpRbacExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $definition = $container->getDefinition(AccessControlDriver::class);
        $definition->addMethodCall('load', [$config]);

        $container->setParameter('symfony_rbac.resolve_target_entities.permission', $config['resolve_target_entities']['permission']);
        $container->setParameter('symfony_rbac.resolve_target_entities.role', $config['resolve_target_entities']['role']);
    }

    public function getAlias(): string
    {
        return 'symfony_rbac';
    }
}
